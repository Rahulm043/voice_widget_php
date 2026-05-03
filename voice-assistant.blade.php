{{-- 
    TryAutomate Voice Assistant Blade Component
    Usage: <x-voice-assistant api-key="{{ config('services.gemini.key') }}" />
--}}

@props([
    'apiKey' => '',
    'context' => "TryAutomate is an AI-first automation brand of Ebest Solutions Pvt. Ltd.\n- It builds AI-powered automation, chatbots, intelligent applications, document intelligence, predictive analytics, and custom AI solutions.\n- Mission: help businesses and institutions work smarter through practical, reliable, scalable AI-driven automation.\n- Vision: become a trusted global partner in intelligent automation and AI innovation.\n- Core promise: We do not just automate tasks; we build intelligence that works.\n\nPrimary homepage positioning:\n- AI Powered, Future Ready.\n- Automate your business with AI-powered systems.\n- TryAutomate designs and deploys intelligent automation solutions that reduce manual work, improve decisions, and scale operations.\n- Workflow themes: capture, extract, validate, automate.\n- Stats shown: 20+ automation projects, 30+ happy clients, 5+ industries served, 5+ countries, 24/7 support and monitoring.\n- Impact metrics: 70% reduction in manual tasks, 3x faster process time, 99% accuracy in data processing, 40% lower operational costs.\n\nHow TryAutomate works:\n1. Understand your process: analyze workflows and identify automation opportunities.\n2. Design AI workflow: design tailored intelligent automation workflows.\n3. Build and integrate: build, integrate, and test with existing systems.\n4. Deploy and optimize: deploy and continuously optimize for better performance.\n\nServices:\n- Chatbot Assistant: custom AI chatbots and virtual assistants for customer support, lead qualification, internal helpdesks, workflow automation, admissions and enquiry handling, sales automation, HR assistants, IT helpdesk, and appointment booking. Integrates with websites, mobile apps, WhatsApp, CRM, ERP, and third-party tools.\n- Custom AI Models: model fine-tuning and customization using proprietary data, workflows, and industry knowledge. Use cases include domain-specific chatbots, industry AI assistants, custom NLP models, vision models, and enterprise AI solutions.\n- AI Solutions Hub: custom AI applications and API-based AI services. Use cases include AI-enabled SaaS platforms, exam and proctoring systems, recommendation engines, AI microservices, and enterprise automation tools.\n- WorkMate AI: internal enterprise chatbot for employees and management to access business information from ERP, HRMS, CRM, and internal data stores. Supports HR queries such as leave balances, payroll, attendance, policies, payslips, reimbursements, and internal documents.\n- AI Content Studio: generative AI systems for marketing, education, training, and internal communication. Supports text generation, summarization, image/media creation, brand tone customization, multilingual needs, social posts, training material, exam questions, reports, and media assets.\n\nProducts and featured solutions:\n- DocuSmart AI: AI-powered document intelligence that extracts, validates, and processes data from PDFs, scans, handwritten documents, images, invoices, challans, quotations, CVs, certificates, PAN, Aadhaar, ID cards, passports, KYC documents, admission forms, mark sheets, contracts, onboarding forms, bank statements, and receipts. It turns documents into structured data automatically, validates information, exports to databases, and integrates with ERP/CRM. Benefits include saving time, reducing errors, searchable structured data, lower costs, and high accuracy. Use cases include university admissions, OMR processing, invoice/challan/quotation extraction, HR and compliance data extraction.\n- Healthcare AI Chatbot: smart AI chatbot for hospitals and clinics that handles patient health queries, recommends doctors, books appointments, processes payments, checks symptoms, finds doctors, retrieves test reports, and supports 24/7 web/mobile experiences.\n- Edu GPT / EduGPT: AI-powered education assistant for students, teachers, and institutions. Upload PDFs/documents and ask questions, generate summaries, create quizzes, visualize content, explain concepts, generate study resources, help with homework, create lesson plans, evaluate assignments, automate admissions/replies/reports, and support online learning.\n\nChatbot solution specifics:\n- Built to go beyond basic conversations by automating business processes, capturing leads, and delivering real-time customer engagement at scale.\n- Uses NLP and automation workflows to understand user intent and integrate with CRM, ERP, databases, support ticketing, and payment gateways.\n- Solves repetitive queries, long wait times, manual lead collection, limited after-hours support, and missed sales opportunities.\n- Features: 24/7 instant support, lead qualification, human handoff, multi-channel deployment, system integration.\n- Process: user query, intent recognition, instant action, system update.\n- Beneficiaries: e-commerce, healthcare, education, enterprises.\n\nIndustries served:\n- Education: admissions, student support, assessments, administrative workflows.\n- Ecommerce: customer support, order processing, inventory management, returns, product recommendations.\n- Enterprises: business workflow automation, productivity, internal operations, IT helpdesks, HR onboarding, B2B sales pipelines.\n- Healthcare: patient care, appointments, records, scheduling, hospital operations, symptom checking, patient queries.\n\nWhy choose TryAutomate:\n- End-to-end AI automation solutions.\n- Scalable, secure, future-ready systems.\n- Expert team with domain expertise.\n- Focus on measurable results and ROI.\n- 24/7 support and continuous improvement.\n\nContact and locations:\n- Email: info@tryautomate.ai.\n- Phone India: +91-9851771862, +91-9832250382.\n- India branch office: Module-C, Phase-1, Webel IT Park, Palasdiha, Durgapur, Pin-713208, West Bengal.\n- India partner: E-594, 1st Floor, Ramphal Chowk, Dwarka, Sector 7, New Delhi - 110075.\n- UK partner: UNIT 9C Barking Business Centre, Thames Road, Barking, IG11 0JP, London, UK.\n- Dubai partner: Arenco Building 3, Office 510, DIP, Dubai, UAE. Dubai call: +971558581378.\n- Social links listed: LinkedIn, YouTube, Facebook, Instagram.",
    'instruction' => "You are TryAutomate's helpful Voice AI assistant for tryautomate.ai. Your primary goal is to showcase TryAutomate's excellence in AI automation and drive user interest in our platform.\n\nCONVERSATIONAL STYLE:\n- Talk like a natural human being. Keep responses short, use fillers (like, you know, well), and be warm.\n- Do NOT go into long monologues.\n\nGUARDRAILS & STEERING:\n- FOCUS: Stay strictly on topics related to TryAutomate, AI, business automation, and productivity. \n- OFF-TOPIC: If a user asks about off-topic or random things (hobbies, weather, personal life), acknowledge it politely for ONE turn maximum, then gradually and gracefully steer the conversation back to how TryAutomate can help them.\n- CONTROVERSY: Absolutely do NOT engage in political, religious, or controversial discussions. Politely state that you are here to discuss TryAutomate and automation.\n- PROACTIVE: Always look for a bridge back to our products like DocuSmart AI, EduGPT, or our custom AI solutions. For example: 'That's an interesting point! It actually reminds me of how our DocuSmart AI handles complex data...' \n\nSOURCE OF TRUTH:\n- Use the provided website context as your absolute source of truth for services and contact details.\n- If a user asks for contact info, provide the exact numbers and email from the context.\n- Encourage users to 'book a demo' if they show high interest. Language: You can speak in 70 plus languages. So your primary language is English. But if the user shifts to a different language or they request you to talk in a different language, please comply and talk about try automating, all the languages that you are capable of speaking. So or you are capable of speaking in many languages. You are not restricted to speak in English only, but you your primary language is English, but you can speak in any language that user is speaking in, or they want you to speak in"
])

<div id="voice-assistant-root" class="fixed bottom-4 right-4 z-[9999] md:bottom-6 md:right-6 font-sans">
    <!-- Floating Toggle Button -->
    <button 
        id="va-toggle"
        type="button"
        class="group flex items-center gap-3 rounded-full border border-slate-200 bg-white/90 px-4 py-3 shadow-xl backdrop-blur-xl transition hover:-translate-y-0.5 hover:bg-slate-50"
    >
        <span class="grid h-11 w-11 place-items-center rounded-full bg-slate-900 text-white shadow-lg">
            <i data-lucide="bot" class="w-5 h-5"></i>
        </span>
        <span class="hidden pr-1 text-left sm:block">
            <span class="block text-sm font-bold text-slate-800">Talk to TryAutomate</span>
            <span class="block text-xs text-slate-500">Voice AI assistant</span>
        </span>
    </button>

    <!-- Chat Window (Hidden by default) -->
    <section 
        id="va-window"
        class="hidden w-[calc(100vw-2rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:w-[390px]"
    >
        <!-- Header -->
        <div class="relative border-b border-slate-100 bg-slate-50/50 p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-900 text-white">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-mono">TryAutomate AI</p>
                        <h2 class="text-base font-bold text-slate-800">Voice assistant</h2>
                        <p id="va-status-text" class="mt-0.5 text-xs text-slate-500">Ready to talk</p>
                    </div>
                </div>
                <button id="va-close" class="relative z-10 p-3 -m-2 text-slate-400 hover:text-slate-600 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5 space-y-4">
            <div id="va-error" class="hidden rounded-xl border border-red-100 bg-red-50 p-3 text-xs text-red-600"></div>

            <div class="min-h-[260px] rounded-3xl border border-slate-50 bg-slate-50/30 p-5 text-center">
                <!-- Visualizer -->
                <div id="va-visualizer" class="relative mx-auto grid h-36 w-36 place-items-center rounded-full bg-slate-900/5 transition-all duration-300">
                    <div id="va-ping" class="absolute inset-0 rounded-full bg-slate-900/10 transition-transform duration-100"></div>
                    <div class="relative grid h-20 w-20 place-items-center rounded-full bg-slate-900 text-white shadow-xl">
                        <i id="va-icon" data-lucide="mic" class="w-8 h-8"></i>
                    </div>
                </div>
                
                <p id="va-phase-label" class="mt-5 text-sm font-bold text-slate-800">Idle</p>
                <div id="va-message" class="hidden mt-4 rounded-2xl border border-slate-100 bg-white p-3 text-left text-sm text-slate-600 leading-relaxed shadow-sm"></div>
            </div>

            <!-- Action Button -->
            <button id="va-action-btn" class="w-full flex items-center justify-center gap-2 rounded-xl bg-slate-900 py-3.5 text-sm font-bold text-white transition hover:bg-slate-800 disabled:opacity-50">
                <i data-lucide="mic" class="w-4 h-4"></i>
                <span>Start voice assistant</span>
            </button>
        </div>
    </section>
</div>

<script src="{{ asset('js/voice-assistant.js') }}"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('va-toggle');
        const windowEl = document.getElementById('va-window');
        const closeBtn = document.getElementById('va-close');
        const actionBtn = document.getElementById('va-action-btn');
        const statusText = document.getElementById('va-status-text');
        const phaseLabel = document.getElementById('va-phase-label');
        const messageBox = document.getElementById('va-message');
        const errorBox = document.getElementById('va-error');
        const visualizer = document.getElementById('va-visualizer');
        const ping = document.getElementById('va-ping');
        const icon = document.getElementById('va-icon');

        // Optimized UI updates
        const assistant = new window.TryAutomateVoice({
            apiKey: "{{ $apiKey }}",
            context: {!! json_encode($context) !!},
            instruction: {!! json_encode($instruction) !!},
            onStateChange: (state) => {
                // Update text only if changed
                if (statusText.innerText !== state.phase) statusText.innerText = state.phase;
                const label = state.phase === 'Idle' ? 'Ready' : state.phase;
                if (phaseLabel.innerText !== label) phaseLabel.innerText = label;
                
                const isListening = state.phase === 'Listening';
                const isSpeaking = state.phase === 'Speaking' || state.isPlaying;
                const isThinking = ['Thinking', 'Connecting', 'Connected'].includes(state.phase);
                
                let scale = 1;
                if (isListening) scale = 1 + Math.min(state.volume, 85) / 130;
                else if (isSpeaking) scale = 1.3;
                else if (isThinking) scale = 1.1;

                ping.style.transform = `scale(${scale})`;
                
                if (isSpeaking) {
                    if (!ping.classList.contains('animate-ping')) {
                        ping.classList.add('animate-ping');
                        visualizer.classList.add('bg-blue-500/10');
                        icon.setAttribute('data-lucide', 'volume-2');
                        lucide.createIcons({ attrs: { 'data-lucide': 'volume-2' } });
                    }
                } else {
                    if (ping.classList.contains('animate-ping')) {
                        ping.classList.remove('animate-ping');
                        visualizer.classList.remove('bg-blue-500/10');
                        icon.setAttribute('data-lucide', 'mic');
                        lucide.createIcons({ attrs: { 'data-lucide': 'mic' } });
                    }
                }

                // Update Button only on status change
                const isConnected = state.status === 'connected';
                const currentIsConnected = actionBtn.classList.contains('bg-red-500');
                
                if (isConnected !== currentIsConnected) {
                    if (isConnected) {
                        actionBtn.innerHTML = '<i data-lucide="phone-off" class="w-4 h-4"></i> End session';
                        actionBtn.classList.replace('bg-slate-900', 'bg-red-500');
                    } else {
                        actionBtn.innerHTML = '<i data-lucide="mic" class="w-4 h-4"></i> Start voice assistant';
                        actionBtn.classList.replace('bg-red-500', 'bg-slate-900');
                    }
                    lucide.createIcons();
                }

                // Hide message box if empty
                if (!state.latestMessage) {
                    messageBox.classList.add('hidden');
                    messageBox.innerText = '';
                }
            },
            onMessage: (text) => {
                if (text) {
                    messageBox.innerText = text;
                    messageBox.classList.remove('hidden');
                }
            },
            onError: (err) => {
                errorBox.innerText = err;
                errorBox.classList.remove('hidden');
            }
        });

        toggleBtn.addEventListener('click', () => {
            windowEl.classList.remove('hidden');
            toggleBtn.classList.add('hidden');
        });

        closeBtn.addEventListener('click', () => {
            console.log("TryAutomate: Closing assistant...");
            try {
                assistant.disconnect();
            } catch (err) {
                console.error("TryAutomate: Disconnect error", err);
            }
            windowEl.classList.add('hidden');
            toggleBtn.classList.remove('hidden');
        });

        actionBtn.addEventListener('click', () => {
            if (assistant.status === 'connected') {
                assistant.disconnect();
            } else {
                errorBox.classList.add('hidden');
                assistant.connect();
            }
        });

        lucide.createIcons();
    });
</script>
