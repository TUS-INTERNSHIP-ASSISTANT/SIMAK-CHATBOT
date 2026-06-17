<section id="tanya-simak" class="py-24 bg-[#FAF7F8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="w-20 h-1 bg-[#7A203A] mx-auto rounded-full mb-6"></div>

            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                Masih Ada Pertanyaan?
            </h2>

            <p class="mt-4 text-gray-600 max-w-3xl mx-auto text-base md:text-lg leading-relaxed">
                Dapatkan informasi mengenai Magang dan Kerja Praktik secara cepat melalui chatbot berbasis AI
                yang siap membantu menjawab pertanyaan Anda kapan saja.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 items-stretch">
            <!-- Left Content -->
            <div class="flex flex-col justify-center">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#7A203A]/10 text-[#7A203A] w-fit mb-5">
                    <span class="text-sm font-medium">Asisten Virtual SIMAK</span>
                </div>

                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
                    Tanya SIMAK dan temukan jawaban lebih cepat.
                </h3>

                <p class="mt-4 text-gray-600 leading-relaxed max-w-xl">
                    Silakan ketik pertanyaan Anda atau pilih pertanyaan yang sering diajukan di bawah ini untuk
                    langsung memulai percakapan dengan SIMAK.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#tanya-simak"
                        class="inline-flex items-center gap-2 bg-[#7A203A] text-white px-6 py-3 rounded-xl font-medium shadow-[0_4px_4px_rgba(205,108,108,0.25)] hover:bg-[#5A182C] hover:-translate-y-0.5 transition-all duration-300">
                        Mulai Bertanya
                    </a>

                    <a href="#alur-program"
                        class="inline-flex items-center gap-2 bg-white text-[#7A203A] border border-[#7A203A]/20 px-6 py-3 rounded-xl font-medium hover:border-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300">
                        Lihat Alur Program
                    </a>
                </div>

                <div class="mt-8 grid sm:grid-cols-3 gap-4 max-w-xl">
                    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900">Cepat</p>
                        <p class="mt-1 text-sm text-gray-500">Jawaban lebih ringkas dan praktis.</p>
                    </div>

                    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900">Efisien</p>
                        <p class="mt-1 text-sm text-gray-500">Berbasis dokumen yang tersedia.</p>
                    </div>

                    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-gray-900">24/7</p>
                        <p class="mt-1 text-sm text-gray-500">Bisa diakses kapan saja.</p>
                    </div>
                </div>
            </div>

            <!-- Right Chat Preview -->
            <div
                class="bg-white border border-[#B77A8A]/30 rounded-[32px] shadow-[0_20px_60px_rgba(122,32,58,0.08)] overflow-hidden">
                <!-- Chat Header -->
                <div
                    class="bg-gradient-to-r from-[#7A203A] to-[#9B2E4A] px-5 sm:px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Robot Icon"
                                class="w-7 h-7 object-contain">
                        </div>

                        <div>
                            <p class="text-white font-semibold leading-tight">Asisten Virtual SIMAK</p>
                            <p class="text-white/80 text-sm">Online • Siap membantu</p>
                        </div>
                    </div>

                    <span
                        class="hidden sm:inline-flex items-center px-3 py-1 rounded-full bg-white/15 text-white text-xs font-medium">
                        AI Chatbot
                    </span>
                </div>

                <!-- Chat Body -->
                <div class="bg-[#FCFAFB] px-4 sm:px-6 py-6">
                    <div class="space-y-4 h-[420px] overflow-y-auto pr-1" id="landing-chat-messages-container">
                        <!-- Bot message -->
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                                <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Bot"
                                    class="w-5 h-5 object-contain">
                            </div>

                            <div class="max-w-[85%]">
                                <div
                                    class="rounded-2xl rounded-tl-sm bg-white border border-gray-100 px-4 py-3 shadow-sm">
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        Halo! 👋 Saya Asisten SIMAK. Silakan tanyakan seputar Magang dan Kerja Praktik.
                                    </p>
                                </div>
                                <p class="mt-1 text-[11px] text-gray-400">SIMAK • Sekarang</p>
                            </div>
                        </div>

                        {{-- Typing Indicator --}}
                        <div class="hidden flex items-start gap-3 animate-pulse" id="landing-chat-typing-indicator">
                            <div
                                class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                                <img src="{{ asset('assets/images/robot-preview.png') }}" alt="Bot"
                                    class="w-5 h-5 object-contain">
                            </div>
                            <div
                                class="bg-white rounded-2xl rounded-tl-sm border border-gray-100 px-4 py-3 shadow-sm flex items-center gap-1.5 py-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce"
                                    style="animation-delay: 0.2s"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce"
                                    style="animation-delay: 0.4s"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Questions -->
                    <div class="mt-6" id="landing-quick-questions">
                        <p class="text-sm font-semibold text-gray-800 mb-3">Pertanyaan</p>

                        <div class="flex flex-wrap gap-3">
                            <button type="button" onclick="quickQuestionLanding('Apa saja syarat Kerja Praktik?')"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300">
                                Apa saja syarat Kerja Praktik?
                            </button>

                            <button type="button" onclick="quickQuestionLanding('Berapa durasi Kerja Praktik?')"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300">
                                Berapa durasi Kerja Praktik?
                            </button>

                            <button type="button" onclick="quickQuestionLanding('Bagaimana alur seminar KP?')"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300">
                                Bagaimana alur seminar KP?
                            </button>

                            <button type="button" onclick="quickQuestionLanding('Bagaimana prosedur pengajuan Magang?')"
                                class="px-4 py-2 rounded-full bg-white border border-gray-200 text-sm text-gray-700 hover:border-[#7A203A] hover:text-[#7A203A] hover:bg-[#7A203A]/5 transition-all duration-300">
                                Bagaimana prosedur pengajuan Magang?
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Input -->
                <div class="border-t border-gray-100 bg-white px-4 sm:px-6 py-4">
                    <form id="landing-chatbot-form" onsubmit="submitLandingChatForm(event)"
                        class="flex flex-col sm:flex-row gap-3">
                        <input type="text" id="landing-chat-query-input"
                            placeholder="Tanya seputar Magang dan Kerja Praktik..." required autocomplete="off"
                            class="flex-1 rounded-xl border border-gray-200 bg-[#FCFAFB] px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-[#7A203A]/20 focus:border-[#7A203A] transition">

                        <button type="submit" id="btn-landing-chat-send"
                            class="inline-flex items-center justify-center gap-2 bg-[#7A203A] text-white px-6 py-3 rounded-xl font-medium hover:bg-[#5A182C] shadow-[0_4px_4px_rgba(205,108,108,0.25)] transition-all duration-300 whitespace-nowrap">
                            Kirim
                        </button>
                    </form>

                    <p class="mt-3 text-[11px] sm:text-xs text-gray-400 text-center leading-relaxed">
                        SIMAK dapat membuat kesalahan. Pastikan kembali informasi penting melalui SSC.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function quickQuestionLanding(text) {
        const input = document.getElementById('landing-chat-query-input');
        if (input) {
            input.value = text;
            input.focus();
            submitLandingChatForm(null);
        }
    }

    function submitLandingChatForm(e) {
        if (e) e.preventDefault();

        const input = document.getElementById('landing-chat-query-input');
        const query = input.value.trim();
        if (!query) return;

        const chatContainer = document.getElementById('landing-chat-messages-container');
        const typingIndicator = document.getElementById('landing-chat-typing-indicator');
        const sendBtn = document.getElementById('btn-landing-chat-send');

        // Disable input & send button
        input.disabled = true;
        sendBtn.disabled = true;

        // Hide quick questions
        const quickQuestions = document.getElementById('landing-quick-questions');
        if (quickQuestions) {
            quickQuestions.classList.add('hidden');
        }

        // Append User message
        appendLandingUserMessage(query);
        input.value = '';

        // Show typing indicator
        typingIndicator.classList.remove('hidden');
        chatContainer.scrollTop = chatContainer.scrollHeight;

        fetch("{{ route('chatbot.query') }}", {
            method: 'POST',
            body: JSON.stringify({ query: query }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    appendLandingBotMessage(data.answer, data.source);
                } else {
                    appendLandingBotMessage('Maaf, saya sedang mengalami kendala. Silakan coba lagi nanti.');
                }
            })
            .catch(err => {
                console.error('Landing chatbot RAG error:', err);
                appendLandingBotMessage('Maaf, terjadi masalah koneksi ke server.');
            })
            .finally(() => {
                // Hide typing indicator
                typingIndicator.classList.add('hidden');
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
    }

    function appendLandingUserMessage(text) {
        const chatContainer = document.getElementById('landing-chat-messages-container');
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + '.' + String(now.getMinutes()).padStart(2, '0');

        const html = `
            <div class="flex items-start justify-end gap-3">
                <div class="max-w-[85%]">
                    <div class="rounded-2xl rounded-tr-sm bg-[#7A203A] px-4 py-3 shadow-sm">
                        <p class="text-sm text-white leading-relaxed">${escapeHTML(text)}</p>
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400 text-right">Anda • ${timeStr}</p>
                </div>
            </div>
        `;

        // Remove typing indicator from bottom to append user message before it
        const typingIndicator = document.getElementById('landing-chat-typing-indicator');
        typingIndicator.insertAdjacentHTML('beforebegin', html);
    }

    function appendLandingBotMessage(text, source = null) {
        const chatContainer = document.getElementById('landing-chat-messages-container');
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + '.' + String(now.getMinutes()).padStart(2, '0');

        let sourceHtml = '';
        if (source) {
            const badgeClass = source.type === 'pdf' ? 'bg-red-50 text-red-600 ring-1 ring-red-100' : (source.type === 'docx' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100');
            sourceHtml = `
                <div class="mt-2 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-[#7A203A]/10 text-[#7A203A] text-[11px] font-semibold">Sumber dokumen</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-[11px] font-semibold ${badgeClass}">${escapeHTML(source.title)}</span>
                </div>
            `;
        }

        const html = `
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm">
                    <img
                        src="{{ asset('assets/images/robot-preview.png') }}"
                        alt="Bot"
                        class="w-5 h-5 object-contain"
                    >
                </div>

                <div class="max-w-[85%]">
                    <div class="rounded-2xl rounded-tl-sm bg-white border border-gray-100 px-4 py-3 shadow-sm text-sm text-gray-700 leading-relaxed">
                        <div class="space-y-2 select-text">${formatMarkdown(text)}</div>
                        ${sourceHtml}
                    </div>
                    <p class="mt-1 text-[11px] text-gray-400">SIMAK • ${timeStr}</p>
                </div>
            </div>
        `;

        const typingIndicator = document.getElementById('landing-chat-typing-indicator');
        typingIndicator.insertAdjacentHTML('beforebegin', html);
    }

    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g,
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
        );
    }

    function formatMarkdown(text) {
        if (!text) return '';

        // First, escape HTML characters to prevent XSS
        let escaped = escapeHTML(text);

        // Split text by lines to handle block elements (headings, lists, paragraphs)
        const lines = escaped.split('\n');
        let htmlResult = [];
        let currentListType = null; // 'ul', 'ol', or null

        function closeList() {
            if (currentListType) {
                htmlResult.push(`</${currentListType}>`);
                currentListType = null;
            }
        }

        for (let i = 0; i < lines.length; i++) {
            let line = lines[i];
            let trimmed = line.trim();

            // 1. Heading Markdown (e.g. ### Heading)
            const headingMatch = line.match(/^(#{1,6})\s+(.+)$/);
            if (headingMatch) {
                closeList();
                const level = headingMatch[1].length;
                const headingText = headingMatch[2];
                const formattedText = formatInlineMarkdown(headingText);
                
                let headingClass = 'font-bold text-gray-900 my-2 block';
                if (level === 1) headingClass += ' text-lg text-[#7A203A]';
                else if (level === 2) headingClass += ' text-md text-[#7A203A]';
                else headingClass += ' text-sm';

                htmlResult.push(`<h${level} class="${headingClass}">${formattedText}</h${level}>`);
                continue;
            }

            // 2. Unordered List Markdown (e.g. - item or * item)
            const ulMatch = line.match(/^(\s*)[-*]\s+(.+)$/);
            if (ulMatch) {
                const listContent = ulMatch[2];
                const formattedContent = formatInlineMarkdown(listContent);
                if (currentListType !== 'ul') {
                    closeList();
                    htmlResult.push('<ul class="list-disc pl-5 my-2 space-y-1">');
                    currentListType = 'ul';
                }
                htmlResult.push(`<li class="text-sm text-gray-700 leading-relaxed">${formattedContent}</li>`);
                continue;
            }

            // 3. Ordered List Markdown (e.g. 1. item)
            const olMatch = line.match(/^(\s*)\d+\.\s+(.+)$/);
            if (olMatch) {
                const listContent = olMatch[2];
                const formattedContent = formatInlineMarkdown(listContent);
                if (currentListType !== 'ol') {
                    closeList();
                    htmlResult.push('<ol class="list-decimal pl-5 my-2 space-y-1">');
                    currentListType = 'ol';
                }
                htmlResult.push(`<li class="text-sm text-gray-700 leading-relaxed">${formattedContent}</li>`);
                continue;
            }

            // 4. Empty lines
            if (trimmed === '') {
                if (currentListType) {
                    continue;
                }
                closeList();
                htmlResult.push('<div class="h-2"></div>');
                continue;
            }

            // 5. Normal text line (or continuation of a paragraph)
            closeList();
            const formattedLine = formatInlineMarkdown(line);
            htmlResult.push(`<p class="text-sm text-gray-700 leading-relaxed mb-2">${formattedLine}</p>`);
        }

        closeList();

        return htmlResult.join('\n');
    }

    function formatInlineMarkdown(text) {
        // Bold: **text**
        let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900">$1</strong>');
        // Italic: *text* or _text_
        formatted = formatted.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
        formatted = formatted.replace(/_(.*?)_/g, '<em class="italic">$1</em>');
        return formatted;
    }
</script>