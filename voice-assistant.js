/**
 * TryAutomate Gemini Voice Assistant - Standalone Logic
 * This file handles the heavy lifting of audio processing and the Gemini Live API connection.
 */

class AudioStreamer {
    constructor(inputSR = 16000, outputSR = 24000) {
        this.inputSampleRate = inputSR;
        this.outputSampleRate = outputSR;
        this.audioContext = null;
        this.stream = null;
        this.analyser = null;
        this.activeSources = [];
        this.nextStartTime = 0;
        this.onPlaybackStateChange = null;
        this.processor = null;
        this.source = null;
    }

    setPlaybackStateListener(listener) {
        this.onPlaybackStateChange = listener;
    }

    async startRecording(onAudioData) {
        if (!this.audioContext) {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)({
                sampleRate: this.inputSampleRate,
            });
        }
        
        if (this.audioContext.state === "suspended") {
            await this.audioContext.resume();
        }

        this.analyser = this.audioContext.createAnalyser();
        this.analyser.fftSize = 256;

        this.stream = await navigator.mediaDevices.getUserMedia({
            audio: { channelCount: 1, echoCancellation: true, noiseSuppression: true },
        });

        this.source = this.audioContext.createMediaStreamSource(this.stream);
        this.processor = this.audioContext.createScriptProcessor(2048, 1, 1);

        this.processor.onaudioprocess = (e) => {
            const inputData = e.inputBuffer.getChannelData(0);
            const pcmData = this.float32ToInt16(inputData);
            onAudioData(this.arrayBufferToBase64(pcmData.buffer));
        };

        this.source.connect(this.analyser);
        this.analyser.connect(this.processor);
        this.processor.connect(this.audioContext.destination);
    }

    stopRecording() {
        if (this.stream) {
            this.stream.getTracks().forEach((t) => t.stop());
            this.stream = null;
        }
        if (this.processor) {
            this.processor.disconnect();
            this.processor = null;
        }
        if (this.source) {
            this.source.disconnect();
            this.source = null;
        }
        this.stopPlayback();
    }

    stopPlayback() {
        this.activeSources.forEach((s) => {
            try {
                s.stop();
                s.disconnect();
            } catch (e) {}
        });
        this.activeSources = [];
        this.nextStartTime = 0;
        if (this.onPlaybackStateChange) this.onPlaybackStateChange(false);
    }

    getVolume() {
        if (!this.analyser) return 0;
        const data = new Uint8Array(this.analyser.frequencyBinCount);
        this.analyser.getByteFrequencyData(data);
        return data.reduce((sum, v) => sum + v, 0) / data.length;
    }

    async addPlaybackChunk(base64Data) {
        if (!this.audioContext) {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (this.audioContext.state === "suspended") {
            await this.audioContext.resume();
        }

        const binary = atob(base64Data);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }

        const float32Data = this.int16ToFloat32(new Int16Array(bytes.buffer));
        const buffer = this.audioContext.createBuffer(1, float32Data.length, this.outputSampleRate);
        buffer.getChannelData(0).set(float32Data);

        const source = this.audioContext.createBufferSource();
        source.buffer = buffer;
        source.connect(this.audioContext.destination);

        const now = this.audioContext.currentTime;
        if (this.nextStartTime < now) this.nextStartTime = now;

        source.start(this.nextStartTime);
        this.nextStartTime += buffer.duration;
        this.activeSources.push(source);

        if (this.onPlaybackStateChange) this.onPlaybackStateChange(true);

        source.onended = () => {
            this.activeSources = this.activeSources.filter((s) => s !== source);
            if (this.activeSources.length === 0 && this.onPlaybackStateChange) {
                this.onPlaybackStateChange(false);
            }
        };
    }

    float32ToInt16(arr) {
        const res = new Int16Array(arr.length);
        for (let i = 0; i < arr.length; i++) {
            const s = Math.max(-1, Math.min(1, arr[i]));
            res[i] = s < 0 ? s * 0x8000 : s * 0x7fff;
        }
        return res;
    }

    int16ToFloat32(arr) {
        const res = new Float32Array(arr.length);
        for (let i = 0; i < arr.length; i++) {
            res[i] = arr[i] / 0x8000;
        }
        return res;
    }

    arrayBufferToBase64(buffer) {
        let bin = "";
        const bytes = new Uint8Array(buffer);
        for (let i = 0; i < bytes.byteLength; i++) {
            bin += String.fromCharCode(bytes[i]);
        }
        return btoa(bin);
    }
}

class GeminiVoiceAssistant {
    constructor(config) {
        this.apiKey = config.apiKey;
        this.systemInstruction = config.systemInstruction;
        this.context = config.context;
        this.onStateChange = config.onStateChange || (() => {});
        this.onMessage = config.onMessage || (() => {});
        this.onError = config.onError || (() => {});

        this.streamer = new AudioStreamer();
        this.session = null;
        this.status = "idle";
        this.phase = "Idle";
        this.isPlaying = false;
        this.volume = 0;
        this.latestMessage = "";

        this.streamer.setPlaybackStateListener((playing) => {
            this.isPlaying = playing;
            this.updatePhase(playing ? "Speaking" : "Listening");
        });
    }

    updatePhase(phase) {
        this.phase = phase;
        this.notify();
    }

    notify() {
        this.onStateChange({
            status: this.status,
            phase: this.phase,
            isPlaying: this.isPlaying,
            volume: this.volume,
            latestMessage: this.latestMessage,
        });
    }

    async connect() {
        if (!this.apiKey) {
            this.onError("API Key is required.");
            return;
        }

        this.status = "connecting";
        this.updatePhase("Connecting");

        try {
            // Dynamically import the GenAI SDK
            const { GoogleGenAI, Modality, StartSensitivity, EndSensitivity, ActivityHandling, TurnCoverage } = 
                await import("https://esm.run/@google/genai");

            const genAI = new GoogleGenAI({ apiKey: this.apiKey });

            this.session = await genAI.live.connect({
                model: "models/gemini-3.1-flash-live-preview",
                config: {
                    systemInstruction: { parts: [{ text: this.systemInstruction + "\n\nCONTEXT:\n" + this.context }] },
                    responseModalities: [Modality.AUDIO],
                    realtimeInputConfig: {
                        automaticActivityDetection: {
                            startOfSpeechSensitivity: StartSensitivity.START_SENSITIVITY_HIGH,
                            endOfSpeechSensitivity: EndSensitivity.END_SENSITIVITY_HIGH,
                            prefixPaddingMs: 80,
                            silenceDurationMs: 260,
                        },
                        activityHandling: ActivityHandling.START_OF_ACTIVITY_INTERRUPTS,
                        turnCoverage: TurnCoverage.TURN_INCLUDES_ONLY_ACTIVITY,
                    },
                    speechConfig: {
                        voiceConfig: {
                            prebuiltVoiceConfig: { voiceName: "Zephyr" },
                        },
                    },
                },
                callbacks: {
                    onopen: () => {
                        this.status = "connected";
                        this.updatePhase("Connected");
                    },
                    onmessage: (msg) => {
                        if (msg.serverContent?.interrupted) {
                            this.streamer.stopPlayback();
                            this.updatePhase("Listening");
                            return;
                        }

                        if (msg.setupComplete) {
                            this.updatePhase("Listening");
                            this.startMic();
                            this.startVolumePolling();
                        }

                        const parts = msg.serverContent?.modelTurn?.parts;
                        if (parts) {
                            parts.forEach((p) => {
                                if (p.inlineData?.data) {
                                    this.streamer.addPlaybackChunk(p.inlineData.data);
                                }
                                if (p.text) {
                                    this.latestMessage = p.text;
                                    this.onMessage(p.text);
                                }
                            });
                        }

                        if (msg.serverContent?.turnComplete) {
                            this.updatePhase("Listening");
                        }
                        this.notify();
                    },
                    onclose: (e) => {
                        console.warn("Session closed:", e);
                        this.disconnect();
                    },
                    onerror: (e) => {
                        console.error("Session error:", e);
                        this.onError(e.message);
                        this.disconnect();
                    },
                },
            });
        } catch (e) {
            this.onError("Connection failed: " + e.message);
            this.status = "error";
            this.notify();
        }
    }

    startMic() {
        this.streamer.startRecording((data) => {
            if (this.session && this.status === "connected") {
                try {
                    this.session.sendRealtimeInput({
                        audio: { data, mimeType: "audio/pcm;rate=16000" },
                    });
                } catch (e) {
                    console.error("Audio send failed", e);
                }
            }
        }).catch(e => {
            this.onError("Microphone access failed: " + e.message);
            this.disconnect();
        });
    }

    startVolumePolling() {
        if (this.volumeInterval) clearInterval(this.volumeInterval);
        this.volumeInterval = setInterval(() => {
            this.volume = this.streamer.getVolume();
            this.notify();
        }, 50);
    }

    disconnect() {
        this.streamer.stopRecording();
        if (this.volumeInterval) clearInterval(this.volumeInterval);
        if (this.session) {
            try {
                this.session.close();
            } catch (e) {}
            this.session = null;
        }
        this.status = "idle";
        this.updatePhase("Idle");
        this.volume = 0;
        this.latestMessage = "";
        this.notify();
    }
}

// Export to window for easy access in Blade
window.TryAutomateVoice = GeminiVoiceAssistant;
