<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryAutomate AI Voice Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        border: "hsl(240 5.9% 90%)",
                        input: "hsl(240 5.9% 90%)",
                        ring: "hsl(240 5.9% 10%)",
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(240 10% 3.9%)",
                        primary: {
                            DEFAULT: "hsl(240 5.9% 10%)",
                            foreground: "hsl(0 0% 98%)",
                            glow: "oklch(0.66 0.19 270)",
                        },
                        surface: "oklch(0.98 0.01 240)",
                        muted: {
                            DEFAULT: "hsl(240 4.8% 95.9%)",
                            foreground: "hsl(240 3.8% 46.1%)",
                        },
                    },
                    borderRadius: { '3xl': '1.5rem' },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            :root {
                --background: 0 0% 100%; --foreground: 240 10% 3.9%; --border: 240 5.9% 90%;
                --primary: 240 5.9% 10%; --primary-foreground: 0 0% 98%;
                --muted: 240 4.8% 95.9%; --muted-foreground: 240 3.8% 46.1%;
            }
        }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, monospace; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-4xl mx-auto">
        <div id="gemini-widget-container" class="relative flex justify-center">
            <div id="widget-root">
                <div class="p-8 text-center text-slate-400">Loading Assistant...</div>
            </div>
        </div>
    </div>

    <script type="module">
        import { 
            GoogleGenAI, Modality, StartSensitivity, EndSensitivity, ActivityHandling, TurnCoverage 
        } from "https://esm.run/@google/genai";

        const TRY_AUTOMATE_CONTEXT = `TryAutomate is an AI-first automation brand of Ebest Solutions Pvt. Ltd. It builds AI-powered automation, chatbots, intelligent applications, document intelligence, predictive analytics, and custom AI solutions. Contact India: +91-9851771862. Email: info@tryautomate.ai.`;
        const SYSTEM_INSTRUCTION = `You are TryAutomate's helpful AI assistant on tryautomate.ai. Answer concisely and professionally. For voice mode, keep responses short.`;

        class AudioStreamer {
            constructor(inputSR = 16000, outputSR = 24000) {
                this.inputSampleRate = inputSR; this.outputSampleRate = outputSR;
                this.audioContext = null; this.stream = null; this.analyser = null;
                this.activeSources = []; this.nextStartTime = 0; this.onPlaybackStateChange = null;
            }
            setPlaybackStateListener(l) { this.onPlaybackStateChange = l; }
            async startRecording(onAudioData) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)({ sampleRate: this.inputSampleRate });
                if (this.audioContext.state === "suspended") await this.audioContext.resume();
                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 256;
                this.stream = await navigator.mediaDevices.getUserMedia({ audio: { channelCount: 1, echoCancellation: true } });
                const source = this.audioContext.createMediaStreamSource(this.stream);
                const processor = this.audioContext.createScriptProcessor(1024, 1, 1);
                processor.onaudioprocess = (e) => {
                    const inputData = e.inputBuffer.getChannelData(0);
                    onAudioData(this.arrayBufferToBase64(this.float32ToInt16(inputData).buffer));
                };
                source.connect(this.analyser); this.analyser.connect(processor); processor.connect(this.audioContext.destination);
                this.processor = processor; this.source = source;
            }
            stopRecording() {
                this.stream?.getTracks().forEach(t => t.stop());
                if(this.processor) this.processor.disconnect(); 
                if(this.source) this.source.disconnect(); 
                this.stopPlayback();
            }
            stopPlayback() {
                this.activeSources.forEach(s => { try { s.stop(); s.disconnect(); } catch(e){} });
                this.activeSources = []; this.nextStartTime = 0; this.onPlaybackStateChange?.(false);
            }
            getVolume() {
                if (!this.analyser) return 0;
                const data = new Uint8Array(this.analyser.frequencyBinCount);
                this.analyser.getByteFrequencyData(data);
                return data.reduce((sum, v) => sum + v, 0) / data.length;
            }
            async addPlaybackChunk(base64Data) {
                if (!this.audioContext) this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                if (this.audioContext.state === "suspended") await this.audioContext.resume();
                const binary = atob(base64Data);
                const bytes = new Uint8Array(binary.length);
                for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
                const float32Data = this.int16ToFloat32(new Int16Array(bytes.buffer));
                const buffer = this.audioContext.createBuffer(1, float32Data.length, this.outputSampleRate);
                buffer.getChannelData(0).set(float32Data);
                const source = this.audioContext.createBufferSource();
                source.buffer = buffer; source.connect(this.audioContext.destination);
                const now = this.audioContext.currentTime;
                if (this.nextStartTime < now) this.nextStartTime = now;
                source.start(this.nextStartTime); this.nextStartTime += buffer.duration;
                this.activeSources.push(source); this.onPlaybackStateChange?.(true);
                source.onended = () => {
                    this.activeSources = this.activeSources.filter(s => s !== source);
                    if (this.activeSources.length === 0) this.onPlaybackStateChange?.(false);
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
                for (let i = 0; i < arr.length; i++) res[i] = arr[i] / 0x8000;
                return res;
            }
            arrayBufferToBase64(buffer) {
                let bin = ""; const bytes = new Uint8Array(buffer);
                for (let i = 0; i < bytes.byteLength; i++) bin += String.fromCharCode(bytes[i]);
                return btoa(bin);
            }
        }

        const state = {
            status: 'idle', phase: 'Idle',
            apiKey: window.INJECTED_GEMINI_API_KEY || localStorage.getItem('tryautomate-gemini-api-key') || '',
            volume: 0, isPlaying: false, latestMessage: '', error: null
        };

        const streamer = new AudioStreamer();
        let session = null;

        function updateUI() {
            const root = document.getElementById('widget-root');
            const connected = state.status === 'connected'; const connecting = state.status === 'connecting';
            const isListening = state.phase === 'Listening';
            const isThinking = ['Thinking', 'Session ready', 'Connected'].includes(state.phase) || connecting;
            const isSpeaking = state.phase === 'Speaking' || state.isPlaying;
            const visualScale = isListening ? 1 + Math.min(state.volume, 85) / 130 : isSpeaking ? 1.34 : isThinking ? 1.16 : 1;

            root.innerHTML = `
                <section class="w-[390px] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                    <div class="relative border-b border-slate-100 bg-slate-50/50 p-5 text-left">
                        <div class="flex items-center gap-3">
                            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-900 text-white"><i data-lucide="sparkles" class="w-5 h-5"></i></div>
                            <div>
                                <p class="mono text-[10px] uppercase tracking-[0.18em] text-slate-400">TryAutomate AI</p>
                                <h2 class="text-base font-semibold tracking-tight text-slate-800">Voice assistant</h2>
                                <p class="mt-0.5 text-xs text-slate-500">${connected || connecting ? state.phase : "Speak with TryAutomate"}</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4 p-5">
                        ${state.error ? `<div class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-600 font-mono">${state.error}</div>` : ''}
                        <div class="min-h-[276px] rounded-3xl border border-slate-100 bg-slate-50/30 p-5 text-center">
                            <div class="relative mx-auto grid h-36 w-36 place-items-center rounded-full transition-colors duration-300 ${isSpeaking ? "bg-blue-500/20" : isThinking ? "bg-amber-500/10" : "bg-blue-500/10"}">
                                <div class="absolute inset-0 rounded-full transition-transform duration-100 ${isSpeaking ? "bg-blue-500/25 animate-ping" : isThinking ? "bg-amber-500/20 animate-pulse" : "bg-blue-500/15"}" style="transform: scale(${visualScale})"></div>
                                <div class="relative grid h-20 w-20 place-items-center rounded-full bg-slate-900 text-white shadow-xl" style="transform: scale(${isListening ? 1 + Math.min(state.volume, 55) / 240 : 1})">
                                    <i data-lucide="${isSpeaking ? 'volume-2' : 'mic'}" class="w-8 h-8"></i>
                                </div>
                            </div>
                            <p class="mt-5 text-sm font-semibold text-slate-800">${isSpeaking ? "Speaking" : isThinking ? "Thinking" : isListening ? "Listening" : "Ready"}</p>
                            ${state.latestMessage ? `<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-3 text-left text-sm text-slate-600">${state.latestMessage}</div>` : ''}
                        </div>
                        ${!connected ? `
                            <button id="connect-btn" class="w-full rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50">
                                ${connecting ? "Connecting..." : "Start voice assistant"}
                            </button>
                        ` : `
                            <button id="disconnect-btn" class="w-full rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-900">End voice session</button>
                        `}
                    </div>
                </section>
            `;
            if (window.lucide) window.lucide.createIcons();
            document.getElementById('connect-btn')?.addEventListener('click', connect);
            document.getElementById('disconnect-btn')?.addEventListener('click', disconnect);
        }

        async function connect() {
            if (!state.apiKey) { state.error = "No API Key found. Check root .env"; updateUI(); return; }
            state.status = 'connecting'; state.phase = 'Connecting'; state.error = null; updateUI();
            try {
                const genAI = new GoogleGenAI({ apiKey: state.apiKey });
                session = await genAI.live.connect({
                    model: "models/gemini-3.1-flash-live-preview",
                    config: {
                        systemInstruction: { parts: [{ text: SYSTEM_INSTRUCTION + "\n\nCONTEXT:\n" + TRY_AUTOMATE_CONTEXT }] },
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
                        onopen: () => { state.status = 'connected'; state.phase = 'Connected'; updateUI(); },
                        onmessage: (msg) => {
                            if (msg.setupComplete) {
                                state.phase = 'Listening';
                                streamer.startRecording((data) => {
                                    if (session) {
                                        try {
                                            session.sendRealtimeInput({ audio: { data, mimeType: "audio/pcm;rate=16000" } });
                                        } catch (e) { console.error("Audio send failed", e); }
                                    }
                                });
                                setInterval(() => { state.volume = streamer.getVolume(); updateUI(); }, 100);
                            }
                            const parts = msg.serverContent?.modelTurn?.parts;
                            if (parts) {
                                parts.forEach(p => {
                                    if (p.inlineData?.data) streamer.addPlaybackChunk(p.inlineData.data);
                                    if (p.text) state.latestMessage = p.text;
                                });
                            }
                            if (msg.serverContent?.turnComplete) state.phase = 'Listening';
                            updateUI();
                        },
                        onclose: (e) => { 
                            console.warn("Session closed:", e);
                            state.error = `Connection closed [Code: ${e.code}] ${e.reason || 'Check API restrictions.'}`;
                            disconnect(); 
                        },
                        onerror: (e) => { console.error("Session error:", e); state.error = `Error: ${e.message}`; disconnect(); }
                    }
                });
            } catch (e) { state.error = `Connection failed: ${e.message}`; state.status = 'error'; updateUI(); }
        }

        function disconnect() {
            streamer.stopRecording();
            if (session) { try { session.close(); } catch(e) {} session = null; }
            state.status = 'idle'; state.phase = 'Idle'; updateUI();
        }

        streamer.setPlaybackStateListener(playing => {
            state.isPlaying = playing; state.phase = playing ? 'Speaking' : 'Listening'; updateUI();
        });

        updateUI();
    </script>
</body>
</html>
