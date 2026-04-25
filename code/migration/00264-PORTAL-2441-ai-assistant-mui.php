<?php

declare(strict_types=1);

use KC\MigrationDatabase\Migrations\MigrationAbstract;
use KC\MigrationDatabase\MUI\ISOLang;
use KC\MigrationDatabase\MUI\MUIKey;

return new class extends MigrationAbstract {
    /**
     * PORTAL-2441 — AI Assistant widget translations (MVP).
     * Group: ai_assistant
     * Languages: EN, AR, ES, FR, DE, PT, HI, BN, UR
     */
    protected function describe(): void
    {
        $this->mui('ai_assistant')
            ->insert(
                // ── 3.1. UI chrome ─────────────────────────────────────────
                MUIKey::make('ai_assistant_fab_label')
                    ->addLang(ISOLang::EN, 'AI Assistant')
                    ->addLang(ISOLang::AR, 'المساعد الذكي')
                    ->addLang(ISOLang::ES, 'Asistente de IA')
                    ->addLang(ISOLang::FR, 'Assistant IA')
                    ->addLang(ISOLang::DE, 'KI-Assistent')
                    ->addLang(ISOLang::PT, 'Assistente de IA')
                    ->addLang(ISOLang::HI, 'एआई सहायक')
                    ->addLang(ISOLang::BN, 'এআই সহকারী')
                    ->addLang(ISOLang::UR, 'اے آئی اسسٹنٹ'),

                MUIKey::make('ai_assistant_fab_badge_label')
                    ->addLang(ISOLang::EN, 'Overdue items')
                    ->addLang(ISOLang::AR, 'عناصر متأخرة')
                    ->addLang(ISOLang::ES, 'Elementos vencidos')
                    ->addLang(ISOLang::FR, 'Éléments en retard')
                    ->addLang(ISOLang::DE, 'Überfällige Einträge')
                    ->addLang(ISOLang::PT, 'Itens vencidos')
                    ->addLang(ISOLang::HI, 'अतिदेय आइटम')
                    ->addLang(ISOLang::BN, 'মেয়াদোত্তীর্ণ আইটেম')
                    ->addLang(ISOLang::UR, 'زائد المیعاد آئٹمز'),

                MUIKey::make('ai_assistant_panel_aria_label')
                    ->addLang(ISOLang::EN, 'AI Student Assistant')
                    ->addLang(ISOLang::AR, 'مساعد الطالب الذكي')
                    ->addLang(ISOLang::ES, 'Asistente de IA para estudiantes')
                    ->addLang(ISOLang::FR, 'Assistant IA pour étudiants')
                    ->addLang(ISOLang::DE, 'KI-Assistent für Lernende')
                    ->addLang(ISOLang::PT, 'Assistente de IA para estudantes')
                    ->addLang(ISOLang::HI, 'एआई छात्र सहायक')
                    ->addLang(ISOLang::BN, 'এআই শিক্ষার্থী সহকারী')
                    ->addLang(ISOLang::UR, 'اے آئی طالب علم اسسٹنٹ'),

                MUIKey::make('ai_assistant_title')
                    ->addLang(ISOLang::EN, 'AI Student Assistant')
                    ->addLang(ISOLang::AR, 'مساعد الطالب الذكي')
                    ->addLang(ISOLang::ES, 'Asistente de IA para estudiantes')
                    ->addLang(ISOLang::FR, 'Assistant IA pour étudiants')
                    ->addLang(ISOLang::DE, 'KI-Assistent für Lernende')
                    ->addLang(ISOLang::PT, 'Assistente de IA para estudantes')
                    ->addLang(ISOLang::HI, 'एआई छात्र सहायक')
                    ->addLang(ISOLang::BN, 'এআই শিক্ষার্থী সহকারী')
                    ->addLang(ISOLang::UR, 'اے آئی طالب علم اسسٹنٹ'),

                MUIKey::make('ai_assistant_subtitle')
                    ->addLang(ISOLang::EN, 'Ask me anything — I\'m here to help.')
                    ->addLang(ISOLang::AR, 'اسألني عن أي شيء — أنا هنا للمساعدة.')
                    ->addLang(ISOLang::ES, 'Pregúntame lo que quieras: estoy aquí para ayudarte.')
                    ->addLang(ISOLang::FR, 'Posez-moi n\'importe quelle question — je suis là pour vous aider.')
                    ->addLang(ISOLang::DE, 'Fragen Sie mich alles — ich bin hier, um zu helfen.')
                    ->addLang(ISOLang::PT, 'Pergunte-me qualquer coisa — estou aqui para ajudar.')
                    ->addLang(ISOLang::HI, 'मुझसे कुछ भी पूछें — मैं मदद के लिए यहाँ हूँ।')
                    ->addLang(ISOLang::BN, 'আমাকে যেকোনো কিছু জিজ্ঞাসা করুন — আমি সাহায্যের জন্য এখানে আছি।')
                    ->addLang(ISOLang::UR, 'مجھ سے کچھ بھی پوچھیں — میں مدد کیلئے حاضر ہوں۔'),

                MUIKey::make('ai_assistant_prompts_heading')
                    ->addLang(ISOLang::EN, 'Suggested for you')
                    ->addLang(ISOLang::AR, 'مقترحات لك')
                    ->addLang(ISOLang::ES, 'Sugerencias para ti')
                    ->addLang(ISOLang::FR, 'Suggestions pour vous')
                    ->addLang(ISOLang::DE, 'Vorschläge für Sie')
                    ->addLang(ISOLang::PT, 'Sugestões para você')
                    ->addLang(ISOLang::HI, 'आपके लिए सुझाव')
                    ->addLang(ISOLang::BN, 'আপনার জন্য প্রস্তাবিত')
                    ->addLang(ISOLang::UR, 'آپ کیلئے تجاویز'),

                MUIKey::make('ai_assistant_loading')
                    ->addLang(ISOLang::EN, 'Loading your assistant…')
                    ->addLang(ISOLang::AR, 'جارٍ تحميل مساعدك…')
                    ->addLang(ISOLang::ES, 'Cargando tu asistente…')
                    ->addLang(ISOLang::FR, 'Chargement de votre assistant…')
                    ->addLang(ISOLang::DE, 'Ihr Assistent wird geladen…')
                    ->addLang(ISOLang::PT, 'Carregando seu assistente…')
                    ->addLang(ISOLang::HI, 'आपका सहायक लोड हो रहा है…')
                    ->addLang(ISOLang::BN, 'আপনার সহকারী লোড হচ্ছে…')
                    ->addLang(ISOLang::UR, 'آپ کا اسسٹنٹ لوڈ ہو رہا ہے…'),

                MUIKey::make('ai_assistant_thinking')
                    ->addLang(ISOLang::EN, 'Thinking')
                    ->addLang(ISOLang::AR, 'جارٍ التفكير')
                    ->addLang(ISOLang::ES, 'Pensando')
                    ->addLang(ISOLang::FR, 'Réflexion en cours')
                    ->addLang(ISOLang::DE, 'Denke nach')
                    ->addLang(ISOLang::PT, 'Pensando')
                    ->addLang(ISOLang::HI, 'विचार हो रहा है')
                    ->addLang(ISOLang::BN, 'চিন্তা করছে')
                    ->addLang(ISOLang::UR, 'سوچا جا رہا ہے'),

                MUIKey::make('ai_assistant_input_placeholder')
                    ->addLang(ISOLang::EN, 'Ask anything…')
                    ->addLang(ISOLang::AR, 'اسأل عن أي شيء…')
                    ->addLang(ISOLang::ES, 'Pregunta lo que quieras…')
                    ->addLang(ISOLang::FR, 'Posez une question…')
                    ->addLang(ISOLang::DE, 'Fragen Sie etwas…')
                    ->addLang(ISOLang::PT, 'Pergunte qualquer coisa…')
                    ->addLang(ISOLang::HI, 'कुछ भी पूछें…')
                    ->addLang(ISOLang::BN, 'যেকোনো কিছু জিজ্ঞাসা করুন…')
                    ->addLang(ISOLang::UR, 'کچھ بھی پوچھیں…'),

                MUIKey::make('ai_assistant_input_aria_label')
                    ->addLang(ISOLang::EN, 'Message input')
                    ->addLang(ISOLang::AR, 'حقل إدخال الرسالة')
                    ->addLang(ISOLang::ES, 'Campo de mensaje')
                    ->addLang(ISOLang::FR, 'Champ de message')
                    ->addLang(ISOLang::DE, 'Nachrichtenfeld')
                    ->addLang(ISOLang::PT, 'Campo de mensagem')
                    ->addLang(ISOLang::HI, 'संदेश इनपुट')
                    ->addLang(ISOLang::BN, 'বার্তা ইনপুট')
                    ->addLang(ISOLang::UR, 'پیغام کا خانہ'),

                MUIKey::make('ai_assistant_send_aria_label')
                    ->addLang(ISOLang::EN, 'Send')
                    ->addLang(ISOLang::AR, 'إرسال')
                    ->addLang(ISOLang::ES, 'Enviar')
                    ->addLang(ISOLang::FR, 'Envoyer')
                    ->addLang(ISOLang::DE, 'Senden')
                    ->addLang(ISOLang::PT, 'Enviar')
                    ->addLang(ISOLang::HI, 'भेजें')
                    ->addLang(ISOLang::BN, 'পাঠান')
                    ->addLang(ISOLang::UR, 'بھیجیں'),

                MUIKey::make('ai_assistant_close_aria_label')
                    ->addLang(ISOLang::EN, 'Close')
                    ->addLang(ISOLang::AR, 'إغلاق')
                    ->addLang(ISOLang::ES, 'Cerrar')
                    ->addLang(ISOLang::FR, 'Fermer')
                    ->addLang(ISOLang::DE, 'Schließen')
                    ->addLang(ISOLang::PT, 'Fechar')
                    ->addLang(ISOLang::HI, 'बंद करें')
                    ->addLang(ISOLang::BN, 'বন্ধ করুন')
                    ->addLang(ISOLang::UR, 'بند کریں'),

                MUIKey::make('ai_assistant_more_options_aria_label')
                    ->addLang(ISOLang::EN, 'More options')
                    ->addLang(ISOLang::AR, 'المزيد من الخيارات')
                    ->addLang(ISOLang::ES, 'Más opciones')
                    ->addLang(ISOLang::FR, 'Plus d\'options')
                    ->addLang(ISOLang::DE, 'Weitere Optionen')
                    ->addLang(ISOLang::PT, 'Mais opções')
                    ->addLang(ISOLang::HI, 'अधिक विकल्प')
                    ->addLang(ISOLang::BN, 'আরও অপশন')
                    ->addLang(ISOLang::UR, 'مزید اختیارات'),

                MUIKey::make('ai_assistant_clear_conversation')
                    ->addLang(ISOLang::EN, 'Clear conversation')
                    ->addLang(ISOLang::AR, 'مسح المحادثة')
                    ->addLang(ISOLang::ES, 'Borrar conversación')
                    ->addLang(ISOLang::FR, 'Effacer la conversation')
                    ->addLang(ISOLang::DE, 'Konversation löschen')
                    ->addLang(ISOLang::PT, 'Limpar conversa')
                    ->addLang(ISOLang::HI, 'बातचीत साफ़ करें')
                    ->addLang(ISOLang::BN, 'কথোপকথন মুছুন')
                    ->addLang(ISOLang::UR, 'گفتگو صاف کریں'),

                MUIKey::make('ai_assistant_retry')
                    ->addLang(ISOLang::EN, 'Try again')
                    ->addLang(ISOLang::AR, 'حاول مرة أخرى')
                    ->addLang(ISOLang::ES, 'Intentar de nuevo')
                    ->addLang(ISOLang::FR, 'Réessayer')
                    ->addLang(ISOLang::DE, 'Erneut versuchen')
                    ->addLang(ISOLang::PT, 'Tentar novamente')
                    ->addLang(ISOLang::HI, 'फिर कोशिश करें')
                    ->addLang(ISOLang::BN, 'আবার চেষ্টা করুন')
                    ->addLang(ISOLang::UR, 'دوبارہ کوشش کریں'),

                MUIKey::make('ai_assistant_footer_note')
                    ->addLang(ISOLang::EN, 'This is powered by AI and may make mistakes.')
                    ->addLang(ISOLang::AR, 'هذه الخدمة تعمل بالذكاء الاصطناعي وقد تصدر عنها أخطاء.')
                    ->addLang(ISOLang::ES, 'Esto funciona con IA y puede cometer errores.')
                    ->addLang(ISOLang::FR, 'Propulsé par l\'IA, des erreurs sont possibles.')
                    ->addLang(ISOLang::DE, 'Dies basiert auf KI und kann Fehler enthalten.')
                    ->addLang(ISOLang::PT, 'Isto é alimentado por IA e pode cometer erros.')
                    ->addLang(ISOLang::HI, 'यह एआई द्वारा संचालित है और इसमें गलतियाँ हो सकती हैं।')
                    ->addLang(ISOLang::BN, 'এটি এআই দ্বারা চালিত এবং ভুল করতে পারে।')
                    ->addLang(ISOLang::UR, 'یہ اے آئی پر مبنی ہے اور غلطیاں کر سکتا ہے۔'),

                // ── 3.2. Overdue banner (pluralized) ───────────────────────
                MUIKey::make('ai_assistant_overdue_you_have')
                    ->addLang(ISOLang::EN, 'You have')
                    ->addLang(ISOLang::AR, 'لديك')
                    ->addLang(ISOLang::ES, 'Tienes')
                    ->addLang(ISOLang::FR, 'Vous avez')
                    ->addLang(ISOLang::DE, 'Sie haben')
                    ->addLang(ISOLang::PT, 'Você tem')
                    ->addLang(ISOLang::HI, 'आपके पास')
                    ->addLang(ISOLang::BN, 'আপনার আছে')
                    ->addLang(ISOLang::UR, 'آپ کے پاس'),

                MUIKey::make('ai_assistant_overdue_item_one')
                    ->addLang(ISOLang::EN, 'overdue item')
                    ->addLang(ISOLang::AR, 'عنصر متأخر')
                    ->addLang(ISOLang::ES, 'elemento vencido')
                    ->addLang(ISOLang::FR, 'élément en retard')
                    ->addLang(ISOLang::DE, 'überfälliger Eintrag')
                    ->addLang(ISOLang::PT, 'item vencido')
                    ->addLang(ISOLang::HI, 'अतिदेय आइटम')
                    ->addLang(ISOLang::BN, 'মেয়াদোত্তীর্ণ আইটেম')
                    ->addLang(ISOLang::UR, 'زائد المیعاد آئٹم'),

                MUIKey::make('ai_assistant_overdue_item_other')
                    ->addLang(ISOLang::EN, 'overdue items')
                    ->addLang(ISOLang::AR, 'عناصر متأخرة')
                    ->addLang(ISOLang::ES, 'elementos vencidos')
                    ->addLang(ISOLang::FR, 'éléments en retard')
                    ->addLang(ISOLang::DE, 'überfällige Einträge')
                    ->addLang(ISOLang::PT, 'itens vencidos')
                    ->addLang(ISOLang::HI, 'अतिदेय आइटम')
                    ->addLang(ISOLang::BN, 'মেয়াদোত্তীর্ণ আইটেম')
                    ->addLang(ISOLang::UR, 'زائد المیعاد آئٹمز'),

                MUIKey::make('ai_assistant_overdue_view')
                    ->addLang(ISOLang::EN, 'View')
                    ->addLang(ISOLang::AR, 'عرض')
                    ->addLang(ISOLang::ES, 'Ver')
                    ->addLang(ISOLang::FR, 'Voir')
                    ->addLang(ISOLang::DE, 'Anzeigen')
                    ->addLang(ISOLang::PT, 'Ver')
                    ->addLang(ISOLang::HI, 'देखें')
                    ->addLang(ISOLang::BN, 'দেখুন')
                    ->addLang(ISOLang::UR, 'دیکھیں'),

                // ── 3.3. Card CTAs ─────────────────────────────────────────
                MUIKey::make('ai_assistant_card_cta_course')
                    ->addLang(ISOLang::EN, 'Go to course')
                    ->addLang(ISOLang::AR, 'الانتقال إلى الدورة التدريبية')
                    ->addLang(ISOLang::ES, 'Ir al curso')
                    ->addLang(ISOLang::FR, 'Aller au cours')
                    ->addLang(ISOLang::DE, 'Zum Kurs')
                    ->addLang(ISOLang::PT, 'Ir ao curso')
                    ->addLang(ISOLang::HI, 'पाठ्यक्रम पर जाएँ')
                    ->addLang(ISOLang::BN, 'কোর্সে যান')
                    ->addLang(ISOLang::UR, 'کورس پر جائیں'),

                MUIKey::make('ai_assistant_card_cta_learning_path')
                    ->addLang(ISOLang::EN, 'Go to learning path')
                    ->addLang(ISOLang::AR, 'الانتقال إلى مسار التعلم')
                    ->addLang(ISOLang::ES, 'Ir a la ruta de aprendizaje')
                    ->addLang(ISOLang::FR, 'Aller au parcours d\'apprentissage')
                    ->addLang(ISOLang::DE, 'Zum Lernpfad')
                    ->addLang(ISOLang::PT, 'Ir ao caminho de aprendizagem')
                    ->addLang(ISOLang::HI, 'सीखने के मार्ग पर जाएँ')
                    ->addLang(ISOLang::BN, 'শেখার পথে যান')
                    ->addLang(ISOLang::UR, 'سیکھنے کے راستے پر جائیں'),

                MUIKey::make('ai_assistant_card_cta_skill_path')
                    ->addLang(ISOLang::EN, 'Go to skill path')
                    ->addLang(ISOLang::AR, 'الانتقال إلى مسار المهارة')
                    ->addLang(ISOLang::ES, 'Ir a la ruta de habilidades')
                    ->addLang(ISOLang::FR, 'Aller au parcours de compétences')
                    ->addLang(ISOLang::DE, 'Zum Fähigkeitspfad')
                    ->addLang(ISOLang::PT, 'Ir ao caminho de habilidades')
                    ->addLang(ISOLang::HI, 'कौशल मार्ग पर जाएँ')
                    ->addLang(ISOLang::BN, 'দক্ষতার পথে যান')
                    ->addLang(ISOLang::UR, 'مہارت کے راستے پر جائیں'),

                // ── 3.4. Context labels (page types) ───────────────────────
                MUIKey::make('ai_assistant_context_general')
                    ->addLang(ISOLang::EN, 'General')
                    ->addLang(ISOLang::AR, 'عام')
                    ->addLang(ISOLang::ES, 'General')
                    ->addLang(ISOLang::FR, 'Général')
                    ->addLang(ISOLang::DE, 'Allgemein')
                    ->addLang(ISOLang::PT, 'Geral')
                    ->addLang(ISOLang::HI, 'सामान्य')
                    ->addLang(ISOLang::BN, 'সাধারণ')
                    ->addLang(ISOLang::UR, 'عمومی'),

                MUIKey::make('ai_assistant_context_assignments')
                    ->addLang(ISOLang::EN, 'Assignments')
                    ->addLang(ISOLang::AR, 'المهام')
                    ->addLang(ISOLang::ES, 'Asignaciones')
                    ->addLang(ISOLang::FR, 'Devoirs')
                    ->addLang(ISOLang::DE, 'Aufgaben')
                    ->addLang(ISOLang::PT, 'Tarefas')
                    ->addLang(ISOLang::HI, 'असाइनमेंट')
                    ->addLang(ISOLang::BN, 'অ্যাসাইনমেন্ট')
                    ->addLang(ISOLang::UR, 'اسائنمنٹس'),

                MUIKey::make('ai_assistant_context_library')
                    ->addLang(ISOLang::EN, 'Course Library')
                    ->addLang(ISOLang::AR, 'مكتبة الدورات التدريبية')
                    ->addLang(ISOLang::ES, 'Biblioteca de cursos')
                    ->addLang(ISOLang::FR, 'Bibliothèque de cours')
                    ->addLang(ISOLang::DE, 'Kursbibliothek')
                    ->addLang(ISOLang::PT, 'Biblioteca de cursos')
                    ->addLang(ISOLang::HI, 'पाठ्यक्रम पुस्तकालय')
                    ->addLang(ISOLang::BN, 'কোর্স লাইব্রেরি')
                    ->addLang(ISOLang::UR, 'کورس لائبریری'),

                MUIKey::make('ai_assistant_context_my_learning')
                    ->addLang(ISOLang::EN, 'My Learning')
                    ->addLang(ISOLang::AR, 'تعلمي')
                    ->addLang(ISOLang::ES, 'Mi aprendizaje')
                    ->addLang(ISOLang::FR, 'Mon apprentissage')
                    ->addLang(ISOLang::DE, 'Mein Lernen')
                    ->addLang(ISOLang::PT, 'Minha aprendizagem')
                    ->addLang(ISOLang::HI, 'मेरी शिक्षा')
                    ->addLang(ISOLang::BN, 'আমার শেখা')
                    ->addLang(ISOLang::UR, 'میری تعلیم'),

                MUIKey::make('ai_assistant_context_my_progress')
                    ->addLang(ISOLang::EN, 'My Progress')
                    ->addLang(ISOLang::AR, 'تقدمي')
                    ->addLang(ISOLang::ES, 'Mi progreso')
                    ->addLang(ISOLang::FR, 'Ma progression')
                    ->addLang(ISOLang::DE, 'Mein Fortschritt')
                    ->addLang(ISOLang::PT, 'Meu progresso')
                    ->addLang(ISOLang::HI, 'मेरी प्रगति')
                    ->addLang(ISOLang::BN, 'আমার অগ্রগতি')
                    ->addLang(ISOLang::UR, 'میری پیشرفت'),

                MUIKey::make('ai_assistant_context_search')
                    ->addLang(ISOLang::EN, 'Search')
                    ->addLang(ISOLang::AR, 'بحث')
                    ->addLang(ISOLang::ES, 'Buscar')
                    ->addLang(ISOLang::FR, 'Recherche')
                    ->addLang(ISOLang::DE, 'Suche')
                    ->addLang(ISOLang::PT, 'Buscar')
                    ->addLang(ISOLang::HI, 'खोजें')
                    ->addLang(ISOLang::BN, 'অনুসন্ধান')
                    ->addLang(ISOLang::UR, 'تلاش'),

                MUIKey::make('ai_assistant_context_settings')
                    ->addLang(ISOLang::EN, 'Settings')
                    ->addLang(ISOLang::AR, 'الإعدادات')
                    ->addLang(ISOLang::ES, 'Configuración')
                    ->addLang(ISOLang::FR, 'Paramètres')
                    ->addLang(ISOLang::DE, 'Einstellungen')
                    ->addLang(ISOLang::PT, 'Configurações')
                    ->addLang(ISOLang::HI, 'सेटिंग्स')
                    ->addLang(ISOLang::BN, 'সেটিংস')
                    ->addLang(ISOLang::UR, 'ترتیبات'),

                MUIKey::make('ai_assistant_context_settings_personal')
                    ->addLang(ISOLang::EN, 'Profile')
                    ->addLang(ISOLang::AR, 'الملف الشخصي')
                    ->addLang(ISOLang::ES, 'Perfil')
                    ->addLang(ISOLang::FR, 'Profil')
                    ->addLang(ISOLang::DE, 'Profil')
                    ->addLang(ISOLang::PT, 'Perfil')
                    ->addLang(ISOLang::HI, 'प्रोफ़ाइल')
                    ->addLang(ISOLang::BN, 'প্রোফাইল')
                    ->addLang(ISOLang::UR, 'پروفائل'),

                MUIKey::make('ai_assistant_context_settings_privacy')
                    ->addLang(ISOLang::EN, 'Security')
                    ->addLang(ISOLang::AR, 'الأمان')
                    ->addLang(ISOLang::ES, 'Seguridad')
                    ->addLang(ISOLang::FR, 'Sécurité')
                    ->addLang(ISOLang::DE, 'Sicherheit')
                    ->addLang(ISOLang::PT, 'Segurança')
                    ->addLang(ISOLang::HI, 'सुरक्षा')
                    ->addLang(ISOLang::BN, 'নিরাপত্তা')
                    ->addLang(ISOLang::UR, 'سیکیورٹی'),

                MUIKey::make('ai_assistant_context_settings_license')
                    ->addLang(ISOLang::EN, 'License')
                    ->addLang(ISOLang::AR, 'الترخيص')
                    ->addLang(ISOLang::ES, 'Licencia')
                    ->addLang(ISOLang::FR, 'Licence')
                    ->addLang(ISOLang::DE, 'Lizenz')
                    ->addLang(ISOLang::PT, 'Licença')
                    ->addLang(ISOLang::HI, 'लाइसेंस')
                    ->addLang(ISOLang::BN, 'লাইসেন্স')
                    ->addLang(ISOLang::UR, 'لائسنس'),

                MUIKey::make('ai_assistant_context_faq')
                    ->addLang(ISOLang::EN, 'FAQ')
                    ->addLang(ISOLang::AR, 'الأسئلة الشائعة')
                    ->addLang(ISOLang::ES, 'Preguntas frecuentes')
                    ->addLang(ISOLang::FR, 'FAQ')
                    ->addLang(ISOLang::DE, 'FAQ')
                    ->addLang(ISOLang::PT, 'Perguntas frequentes')
                    ->addLang(ISOLang::HI, 'अक्सर पूछे जाने वाले प्रश्न')
                    ->addLang(ISOLang::BN, 'সাধারণ জিজ্ঞাসা')
                    ->addLang(ISOLang::UR, 'اکثر پوچھے گئے سوالات'),

                MUIKey::make('ai_assistant_context_webinar')
                    ->addLang(ISOLang::EN, 'Webinars')
                    ->addLang(ISOLang::AR, 'الندوات الافتراضية')
                    ->addLang(ISOLang::ES, 'Seminarios web')
                    ->addLang(ISOLang::FR, 'Webinaires')
                    ->addLang(ISOLang::DE, 'Webinare')
                    ->addLang(ISOLang::PT, 'Webinars')
                    ->addLang(ISOLang::HI, 'वेबिनार')
                    ->addLang(ISOLang::BN, 'ওয়েবিনার')
                    ->addLang(ISOLang::UR, 'ویبینارز'),

                MUIKey::make('ai_assistant_context_calendar')
                    ->addLang(ISOLang::EN, 'Calendar')
                    ->addLang(ISOLang::AR, 'التقويم')
                    ->addLang(ISOLang::ES, 'Calendario')
                    ->addLang(ISOLang::FR, 'Calendrier')
                    ->addLang(ISOLang::DE, 'Kalender')
                    ->addLang(ISOLang::PT, 'Calendário')
                    ->addLang(ISOLang::HI, 'कैलेंडर')
                    ->addLang(ISOLang::BN, 'ক্যালেন্ডার')
                    ->addLang(ISOLang::UR, 'کیلنڈر'),

                MUIKey::make('ai_assistant_context_saved')
                    ->addLang(ISOLang::EN, 'Saved Courses')
                    ->addLang(ISOLang::AR, 'الدورات المحفوظة')
                    ->addLang(ISOLang::ES, 'Cursos guardados')
                    ->addLang(ISOLang::FR, 'Cours enregistrés')
                    ->addLang(ISOLang::DE, 'Gespeicherte Kurse')
                    ->addLang(ISOLang::PT, 'Cursos salvos')
                    ->addLang(ISOLang::HI, 'सहेजे गए पाठ्यक्रम')
                    ->addLang(ISOLang::BN, 'সংরক্ষিত কোর্স')
                    ->addLang(ISOLang::UR, 'محفوظ شدہ کورسز'),

                MUIKey::make('ai_assistant_context_home')
                    ->addLang(ISOLang::EN, 'Home')
                    ->addLang(ISOLang::AR, 'الرئيسية')
                    ->addLang(ISOLang::ES, 'Inicio')
                    ->addLang(ISOLang::FR, 'Accueil')
                    ->addLang(ISOLang::DE, 'Startseite')
                    ->addLang(ISOLang::PT, 'Início')
                    ->addLang(ISOLang::HI, 'मुख्य पृष्ठ')
                    ->addLang(ISOLang::BN, 'হোম')
                    ->addLang(ISOLang::UR, 'ہوم'),

                // ── 3.5. Error messages ────────────────────────────────────
                MUIKey::make('ai_assistant_error_connection_lost')
                    ->addLang(ISOLang::EN, 'Connection was lost.')
                    ->addLang(ISOLang::AR, 'انقطع الاتصال.')
                    ->addLang(ISOLang::ES, 'Se perdió la conexión.')
                    ->addLang(ISOLang::FR, 'La connexion a été perdue.')
                    ->addLang(ISOLang::DE, 'Die Verbindung wurde unterbrochen.')
                    ->addLang(ISOLang::PT, 'A conexão foi perdida.')
                    ->addLang(ISOLang::HI, 'कनेक्शन टूट गया।')
                    ->addLang(ISOLang::BN, 'সংযোগ বিচ্ছিন্ন হয়েছে।')
                    ->addLang(ISOLang::UR, 'کنکشن منقطع ہو گیا۔'),

                MUIKey::make('ai_assistant_error_rate_limited')
                    ->addLang(ISOLang::EN, 'AI is temporarily rate-limited.')
                    ->addLang(ISOLang::AR, 'الذكاء الاصطناعي محدود مؤقتًا.')
                    ->addLang(ISOLang::ES, 'La IA está temporalmente limitada.')
                    ->addLang(ISOLang::FR, 'L\'IA est temporairement limitée.')
                    ->addLang(ISOLang::DE, 'Die KI ist vorübergehend begrenzt.')
                    ->addLang(ISOLang::PT, 'A IA está temporariamente limitada.')
                    ->addLang(ISOLang::HI, 'एआई की दर अस्थायी रूप से सीमित है।')
                    ->addLang(ISOLang::BN, 'এআই সাময়িকভাবে সীমাবদ্ধ।')
                    ->addLang(ISOLang::UR, 'اے آئی عارضی طور پر محدود ہے۔'),

                MUIKey::make('ai_assistant_error_rate_limited_wait')
                    ->addLang(ISOLang::EN, 'AI is temporarily rate-limited. Available in {wait}.')
                    ->addLang(ISOLang::AR, 'الذكاء الاصطناعي محدود مؤقتًا. متاح خلال {wait}.')
                    ->addLang(ISOLang::ES, 'La IA está temporalmente limitada. Disponible en {wait}.')
                    ->addLang(ISOLang::FR, 'L\'IA est temporairement limitée. Disponible dans {wait}.')
                    ->addLang(ISOLang::DE, 'Die KI ist vorübergehend begrenzt. Verfügbar in {wait}.')
                    ->addLang(ISOLang::PT, 'A IA está temporariamente limitada. Disponível em {wait}.')
                    ->addLang(ISOLang::HI, 'एआई की दर अस्थायी रूप से सीमित है। {wait} में उपलब्ध होगी।')
                    ->addLang(ISOLang::BN, 'এআই সাময়িকভাবে সীমাবদ্ধ। {wait} এ উপলব্ধ হবে।')
                    ->addLang(ISOLang::UR, 'اے آئی عارضی طور پر محدود ہے۔ {wait} میں دستیاب ہوگا۔'),

                MUIKey::make('ai_assistant_error_too_long')
                    ->addLang(ISOLang::EN, 'Your message is too long for the AI. Shorten it.')
                    ->addLang(ISOLang::AR, 'رسالتك طويلة جدًا بالنسبة للذكاء الاصطناعي. يُرجى تقصيرها.')
                    ->addLang(ISOLang::ES, 'Tu mensaje es demasiado largo para la IA. Acórtalo.')
                    ->addLang(ISOLang::FR, 'Votre message est trop long pour l\'IA. Raccourcissez-le.')
                    ->addLang(ISOLang::DE, 'Ihre Nachricht ist zu lang für die KI. Bitte kürzen.')
                    ->addLang(ISOLang::PT, 'Sua mensagem é muito longa para a IA. Encurte-a.')
                    ->addLang(ISOLang::HI, 'आपका संदेश एआई के लिए बहुत लंबा है। इसे छोटा करें।')
                    ->addLang(ISOLang::BN, 'আপনার বার্তাটি এআই-এর জন্য খুব দীর্ঘ। এটি সংক্ষিপ্ত করুন।')
                    ->addLang(ISOLang::UR, 'آپ کا پیغام اے آئی کیلئے بہت طویل ہے۔ اسے مختصر کریں۔'),

                MUIKey::make('ai_assistant_error_timeout')
                    ->addLang(ISOLang::EN, 'The AI took too long to respond.')
                    ->addLang(ISOLang::AR, 'استغرق الذكاء الاصطناعي وقتًا طويلاً للرد.')
                    ->addLang(ISOLang::ES, 'La IA tardó demasiado en responder.')
                    ->addLang(ISOLang::FR, 'L\'IA a mis trop de temps à répondre.')
                    ->addLang(ISOLang::DE, 'Die KI hat zu lange für eine Antwort gebraucht.')
                    ->addLang(ISOLang::PT, 'A IA demorou muito para responder.')
                    ->addLang(ISOLang::HI, 'एआई को उत्तर देने में बहुत समय लगा।')
                    ->addLang(ISOLang::BN, 'এআই সাড়া দিতে অনেক সময় নিয়েছে।')
                    ->addLang(ISOLang::UR, 'اے آئی کو جواب دینے میں بہت وقت لگا۔'),

                MUIKey::make('ai_assistant_error_unavailable')
                    ->addLang(ISOLang::EN, 'AI service is temporarily unavailable.')
                    ->addLang(ISOLang::AR, 'خدمة الذكاء الاصطناعي غير متاحة مؤقتًا.')
                    ->addLang(ISOLang::ES, 'El servicio de IA no está disponible temporalmente.')
                    ->addLang(ISOLang::FR, 'Le service d\'IA est temporairement indisponible.')
                    ->addLang(ISOLang::DE, 'Der KI-Dienst ist vorübergehend nicht verfügbar.')
                    ->addLang(ISOLang::PT, 'O serviço de IA está temporariamente indisponível.')
                    ->addLang(ISOLang::HI, 'एआई सेवा अस्थायी रूप से उपलब्ध नहीं है।')
                    ->addLang(ISOLang::BN, 'এআই পরিষেবা সাময়িকভাবে অনুপলব্ধ।')
                    ->addLang(ISOLang::UR, 'اے آئی سروس عارضی طور پر دستیاب نہیں ہے۔'),

                MUIKey::make('ai_assistant_error_network')
                    ->addLang(ISOLang::EN, 'Network connection was lost. Check your connection.')
                    ->addLang(ISOLang::AR, 'انقطع الاتصال بالشبكة. يُرجى التحقق من اتصالك.')
                    ->addLang(ISOLang::ES, 'Se perdió la conexión de red. Verifica tu conexión.')
                    ->addLang(ISOLang::FR, 'La connexion réseau a été perdue. Vérifiez votre connexion.')
                    ->addLang(ISOLang::DE, 'Die Netzwerkverbindung wurde unterbrochen. Prüfen Sie Ihre Verbindung.')
                    ->addLang(ISOLang::PT, 'A conexão de rede foi perdida. Verifique sua conexão.')
                    ->addLang(ISOLang::HI, 'नेटवर्क कनेक्शन टूट गया। अपना कनेक्शन जाँचें।')
                    ->addLang(ISOLang::BN, 'নেটওয়ার্ক সংযোগ বিচ্ছিন্ন হয়েছে। আপনার সংযোগ পরীক্ষা করুন।')
                    ->addLang(ISOLang::UR, 'نیٹ ورک کنکشن منقطع ہو گیا۔ اپنا کنکشن چیک کریں۔'),

                // ── 3.6. Suggested prompts ─────────────────────────────────
                // assignments
                MUIKey::make('ai_assistant_prompt_assignments_1')
                    ->addLang(ISOLang::EN, 'What are my upcoming deadlines?')
                    ->addLang(ISOLang::AR, 'ما هي المواعيد النهائية القادمة؟')
                    ->addLang(ISOLang::ES, '¿Cuáles son mis próximas fechas límite?')
                    ->addLang(ISOLang::FR, 'Quelles sont mes prochaines échéances ?')
                    ->addLang(ISOLang::DE, 'Was sind meine nächsten Fristen?')
                    ->addLang(ISOLang::PT, 'Quais são meus próximos prazos?')
                    ->addLang(ISOLang::HI, 'मेरी आगामी समय-सीमाएँ क्या हैं?')
                    ->addLang(ISOLang::BN, 'আমার আসন্ন সময়সীমা কী কী?')
                    ->addLang(ISOLang::UR, 'میری آنے والی ڈیڈ لائنز کیا ہیں؟'),

                MUIKey::make('ai_assistant_prompt_assignments_2')
                    ->addLang(ISOLang::EN, 'Which assignments should I prioritize?')
                    ->addLang(ISOLang::AR, 'ما المهام التي ينبغي أن أعطيها الأولوية؟')
                    ->addLang(ISOLang::ES, '¿Qué asignaciones debo priorizar?')
                    ->addLang(ISOLang::FR, 'Quels devoirs dois-je prioriser ?')
                    ->addLang(ISOLang::DE, 'Welche Aufgaben sollte ich priorisieren?')
                    ->addLang(ISOLang::PT, 'Quais tarefas devo priorizar?')
                    ->addLang(ISOLang::HI, 'मुझे किन असाइनमेंट्स को प्राथमिकता देनी चाहिए?')
                    ->addLang(ISOLang::BN, 'আমার কোন অ্যাসাইনমেন্টগুলোকে অগ্রাধিকার দেওয়া উচিত?')
                    ->addLang(ISOLang::UR, 'مجھے کن اسائنمنٹس کو ترجیح دینی چاہیے؟'),

                MUIKey::make('ai_assistant_prompt_assignments_3')
                    ->addLang(ISOLang::EN, 'Do I have any overdue assignments?')
                    ->addLang(ISOLang::AR, 'هل لدي أي مهام متأخرة؟')
                    ->addLang(ISOLang::ES, '¿Tengo alguna asignación vencida?')
                    ->addLang(ISOLang::FR, 'Ai-je des devoirs en retard ?')
                    ->addLang(ISOLang::DE, 'Habe ich überfällige Aufgaben?')
                    ->addLang(ISOLang::PT, 'Tenho alguma tarefa vencida?')
                    ->addLang(ISOLang::HI, 'क्या मेरे पास कोई अतिदेय असाइनमेंट है?')
                    ->addLang(ISOLang::BN, 'আমার কি কোনো মেয়াদোত্তীর্ণ অ্যাসাইনমেন্ট আছে?')
                    ->addLang(ISOLang::UR, 'کیا میرے پاس کوئی زائد المیعاد اسائنمنٹس ہیں؟'),

                // library
                MUIKey::make('ai_assistant_prompt_library_1')
                    ->addLang(ISOLang::EN, 'What skills do I need to develop?')
                    ->addLang(ISOLang::AR, 'ما المهارات التي أحتاج إلى تطويرها؟')
                    ->addLang(ISOLang::ES, '¿Qué habilidades necesito desarrollar?')
                    ->addLang(ISOLang::FR, 'Quelles compétences dois-je développer ?')
                    ->addLang(ISOLang::DE, 'Welche Fähigkeiten muss ich entwickeln?')
                    ->addLang(ISOLang::PT, 'Quais habilidades preciso desenvolver?')
                    ->addLang(ISOLang::HI, 'मुझे कौन-से कौशल विकसित करने की आवश्यकता है?')
                    ->addLang(ISOLang::BN, 'আমার কোন দক্ষতা গড়ে তোলা দরকার?')
                    ->addLang(ISOLang::UR, 'مجھے کون سی مہارتیں ترقی دینے کی ضرورت ہے؟'),

                MUIKey::make('ai_assistant_prompt_library_2')
                    ->addLang(ISOLang::EN, 'Recommend courses to fill my skill gaps')
                    ->addLang(ISOLang::AR, 'اقترح دورات تدريبية لسد الفجوات في مهاراتي')
                    ->addLang(ISOLang::ES, 'Recomiéndame cursos para cubrir mis brechas de habilidades')
                    ->addLang(ISOLang::FR, 'Recommandez des cours pour combler mes lacunes')
                    ->addLang(ISOLang::DE, 'Empfehlen Sie Kurse, um meine Fähigkeitslücken zu schließen')
                    ->addLang(ISOLang::PT, 'Recomende cursos para preencher minhas lacunas de habilidades')
                    ->addLang(ISOLang::HI, 'मेरे कौशल अंतराल को भरने के लिए पाठ्यक्रमों की अनुशंसा करें')
                    ->addLang(ISOLang::BN, 'আমার দক্ষতার ঘাটতি পূরণে কোর্স সুপারিশ করুন')
                    ->addLang(ISOLang::UR, 'میری مہارتوں کی کمی پوری کرنے کیلئے کورسز تجویز کریں'),

                MUIKey::make('ai_assistant_prompt_library_3')
                    ->addLang(ISOLang::EN, 'What should I learn next?')
                    ->addLang(ISOLang::AR, 'ماذا ينبغي أن أتعلم بعد ذلك؟')
                    ->addLang(ISOLang::ES, '¿Qué debería aprender a continuación?')
                    ->addLang(ISOLang::FR, 'Que devrais-je apprendre ensuite ?')
                    ->addLang(ISOLang::DE, 'Was sollte ich als Nächstes lernen?')
                    ->addLang(ISOLang::PT, 'O que devo aprender em seguida?')
                    ->addLang(ISOLang::HI, 'मुझे आगे क्या सीखना चाहिए?')
                    ->addLang(ISOLang::BN, 'আমার পরবর্তীতে কী শেখা উচিত?')
                    ->addLang(ISOLang::UR, 'مجھے آگے کیا سیکھنا چاہیے؟'),

                // my-learning
                MUIKey::make('ai_assistant_prompt_my_learning_1')
                    ->addLang(ISOLang::EN, 'What should I focus on next?')
                    ->addLang(ISOLang::AR, 'علامَ ينبغي أن أركز بعد ذلك؟')
                    ->addLang(ISOLang::ES, '¿En qué debería concentrarme a continuación?')
                    ->addLang(ISOLang::FR, 'Sur quoi devrais-je me concentrer ensuite ?')
                    ->addLang(ISOLang::DE, 'Worauf sollte ich mich als Nächstes konzentrieren?')
                    ->addLang(ISOLang::PT, 'Em que devo focar em seguida?')
                    ->addLang(ISOLang::HI, 'मुझे आगे किस पर ध्यान केंद्रित करना चाहिए?')
                    ->addLang(ISOLang::BN, 'আমার পরবর্তীতে কীসে মনোনিবেশ করা উচিত?')
                    ->addLang(ISOLang::UR, 'مجھے آگے کس چیز پر توجہ دینی چاہیے؟'),

                MUIKey::make('ai_assistant_prompt_my_learning_2')
                    ->addLang(ISOLang::EN, 'Show my learning progress')
                    ->addLang(ISOLang::AR, 'اعرض تقدمي في التعلم')
                    ->addLang(ISOLang::ES, 'Muestra mi progreso de aprendizaje')
                    ->addLang(ISOLang::FR, 'Afficher ma progression d\'apprentissage')
                    ->addLang(ISOLang::DE, 'Zeigen Sie meinen Lernfortschritt')
                    ->addLang(ISOLang::PT, 'Mostre meu progresso de aprendizagem')
                    ->addLang(ISOLang::HI, 'मेरी सीखने की प्रगति दिखाएँ')
                    ->addLang(ISOLang::BN, 'আমার শেখার অগ্রগতি দেখান')
                    ->addLang(ISOLang::UR, 'میری سیکھنے کی پیشرفت دکھائیں'),

                MUIKey::make('ai_assistant_prompt_my_learning_3')
                    ->addLang(ISOLang::EN, 'What skills am I building?')
                    ->addLang(ISOLang::AR, 'ما المهارات التي أطورها؟')
                    ->addLang(ISOLang::ES, '¿Qué habilidades estoy desarrollando?')
                    ->addLang(ISOLang::FR, 'Quelles compétences suis-je en train de développer ?')
                    ->addLang(ISOLang::DE, 'Welche Fähigkeiten baue ich auf?')
                    ->addLang(ISOLang::PT, 'Quais habilidades estou desenvolvendo?')
                    ->addLang(ISOLang::HI, 'कौन-से कौशल विकसित हो रहे हैं?')
                    ->addLang(ISOLang::BN, 'আমি কোন দক্ষতা গড়ে তুলছি?')
                    ->addLang(ISOLang::UR, 'کون سی مہارتیں بن رہی ہیں؟'),

                // my-progress
                MUIKey::make('ai_assistant_prompt_my_progress_1')
                    ->addLang(ISOLang::EN, 'Summarize my progress this month')
                    ->addLang(ISOLang::AR, 'لخص تقدمي خلال هذا الشهر')
                    ->addLang(ISOLang::ES, 'Resume mi progreso este mes')
                    ->addLang(ISOLang::FR, 'Résumez ma progression ce mois-ci')
                    ->addLang(ISOLang::DE, 'Fassen Sie meinen Fortschritt in diesem Monat zusammen')
                    ->addLang(ISOLang::PT, 'Resuma meu progresso este mês')
                    ->addLang(ISOLang::HI, 'इस माह की मेरी प्रगति का सारांश दें')
                    ->addLang(ISOLang::BN, 'এই মাসে আমার অগ্রগতি সংক্ষেপে বলুন')
                    ->addLang(ISOLang::UR, 'اس ماہ میری پیشرفت کا خلاصہ دیں'),

                MUIKey::make('ai_assistant_prompt_my_progress_2')
                    ->addLang(ISOLang::EN, 'Which courses did I study recently?')
                    ->addLang(ISOLang::AR, 'ما الدورات التدريبية التي درستُها مؤخرًا؟')
                    ->addLang(ISOLang::ES, '¿Qué cursos estudié recientemente?')
                    ->addLang(ISOLang::FR, 'Quels cours ai-je étudiés récemment ?')
                    ->addLang(ISOLang::DE, 'Welche Kurse habe ich zuletzt studiert?')
                    ->addLang(ISOLang::PT, 'Quais cursos estudei recentemente?')
                    ->addLang(ISOLang::HI, 'मैंने हाल ही में कौन-से पाठ्यक्रम पढ़े?')
                    ->addLang(ISOLang::BN, 'আমি সম্প্রতি কোন কোর্সগুলি পড়েছি?')
                    ->addLang(ISOLang::UR, 'میں نے حال ہی میں کون سے کورسز پڑھے؟'),

                MUIKey::make('ai_assistant_prompt_my_progress_3')
                    ->addLang(ISOLang::EN, 'What is a learning cycle?')
                    ->addLang(ISOLang::AR, 'ما دورة التعلم؟')
                    ->addLang(ISOLang::ES, '¿Qué es un ciclo de aprendizaje?')
                    ->addLang(ISOLang::FR, 'Qu\'est-ce qu\'un cycle d\'apprentissage ?')
                    ->addLang(ISOLang::DE, 'Was ist ein Lernzyklus?')
                    ->addLang(ISOLang::PT, 'O que é um ciclo de aprendizagem?')
                    ->addLang(ISOLang::HI, 'सीखने का चक्र क्या है?')
                    ->addLang(ISOLang::BN, 'শেখার চক্র কী?')
                    ->addLang(ISOLang::UR, 'سیکھنے کا چکر کیا ہے؟'),

                // search
                MUIKey::make('ai_assistant_prompt_search_1')
                    ->addLang(ISOLang::EN, 'Help me find courses about leadership')
                    ->addLang(ISOLang::AR, 'ساعدني في العثور على دورات تدريبية في القيادة')
                    ->addLang(ISOLang::ES, 'Ayúdame a encontrar cursos sobre liderazgo')
                    ->addLang(ISOLang::FR, 'Aidez-moi à trouver des cours sur le leadership')
                    ->addLang(ISOLang::DE, 'Helfen Sie mir, Kurse über Führung zu finden')
                    ->addLang(ISOLang::PT, 'Ajude-me a encontrar cursos sobre liderança')
                    ->addLang(ISOLang::HI, 'नेतृत्व पर पाठ्यक्रम खोजने में मेरी मदद करें')
                    ->addLang(ISOLang::BN, 'নেতৃত্ব সম্পর্কিত কোর্স খুঁজে পেতে সাহায্য করুন')
                    ->addLang(ISOLang::UR, 'قیادت کے بارے میں کورسز تلاش کرنے میں مدد کریں'),

                MUIKey::make('ai_assistant_prompt_search_2')
                    ->addLang(ISOLang::EN, 'What topics should I search for?')
                    ->addLang(ISOLang::AR, 'ما الموضوعات التي ينبغي أن أبحث عنها؟')
                    ->addLang(ISOLang::ES, '¿Qué temas debería buscar?')
                    ->addLang(ISOLang::FR, 'Quels sujets devrais-je rechercher ?')
                    ->addLang(ISOLang::DE, 'Nach welchen Themen sollte ich suchen?')
                    ->addLang(ISOLang::PT, 'Que tópicos devo pesquisar?')
                    ->addLang(ISOLang::HI, 'मुझे किन विषयों की खोज करनी चाहिए?')
                    ->addLang(ISOLang::BN, 'আমার কোন বিষয়গুলি খোঁজা উচিত?')
                    ->addLang(ISOLang::UR, 'مجھے کن موضوعات کی تلاش کرنی چاہیے؟'),

                MUIKey::make('ai_assistant_prompt_search_3')
                    ->addLang(ISOLang::EN, 'Recommend courses to fill my skill gaps')
                    ->addLang(ISOLang::AR, 'اقترح دورات تدريبية لسد الفجوات في مهاراتي')
                    ->addLang(ISOLang::ES, 'Recomiéndame cursos para cubrir mis brechas de habilidades')
                    ->addLang(ISOLang::FR, 'Recommandez des cours pour combler mes lacunes')
                    ->addLang(ISOLang::DE, 'Empfehlen Sie Kurse, um meine Fähigkeitslücken zu schließen')
                    ->addLang(ISOLang::PT, 'Recomende cursos para preencher minhas lacunas de habilidades')
                    ->addLang(ISOLang::HI, 'मेरे कौशल अंतराल को भरने के लिए पाठ्यक्रमों की अनुशंसा करें')
                    ->addLang(ISOLang::BN, 'আমার দক্ষতার ঘাটতি পূরণে কোর্স সুপারিশ করুন')
                    ->addLang(ISOLang::UR, 'میری مہارتوں کی کمی پوری کرنے کیلئے کورسز تجویز کریں'),

                // saved
                MUIKey::make('ai_assistant_prompt_saved_1')
                    ->addLang(ISOLang::EN, 'Which saved course should I start first?')
                    ->addLang(ISOLang::AR, 'أي دورة تدريبية محفوظة ينبغي أن أبدأ بها أولاً؟')
                    ->addLang(ISOLang::ES, '¿Qué curso guardado debería empezar primero?')
                    ->addLang(ISOLang::FR, 'Par quel cours enregistré devrais-je commencer ?')
                    ->addLang(ISOLang::DE, 'Welchen gespeicherten Kurs sollte ich zuerst beginnen?')
                    ->addLang(ISOLang::PT, 'Por qual curso salvo devo começar primeiro?')
                    ->addLang(ISOLang::HI, 'मुझे पहले कौन-सा सहेजा गया पाठ्यक्रम शुरू करना चाहिए?')
                    ->addLang(ISOLang::BN, 'আমি কোন সংরক্ষিত কোর্স প্রথমে শুরু করব?')
                    ->addLang(ISOLang::UR, 'مجھے پہلے کون سا محفوظ شدہ کورس شروع کرنا چاہیے؟'),

                MUIKey::make('ai_assistant_prompt_saved_2')
                    ->addLang(ISOLang::EN, 'Do any saved courses match my skill gaps?')
                    ->addLang(ISOLang::AR, 'هل تغطي أي من الدورات التدريبية المحفوظة الفجوات في مهاراتي؟')
                    ->addLang(ISOLang::ES, '¿Hay cursos guardados que coincidan con mis brechas de habilidades?')
                    ->addLang(ISOLang::FR, 'Des cours enregistrés correspondent-ils à mes lacunes ?')
                    ->addLang(ISOLang::DE, 'Passen gespeicherte Kurse zu meinen Fähigkeitslücken?')
                    ->addLang(ISOLang::PT, 'Algum curso salvo corresponde às minhas lacunas de habilidades?')
                    ->addLang(ISOLang::HI, 'क्या कोई सहेजा गया पाठ्यक्रम मेरे कौशल अंतराल से मेल खाता है?')
                    ->addLang(ISOLang::BN, 'কোনো সংরক্ষিত কোর্স কি আমার দক্ষতার ঘাটতির সঙ্গে মিলে?')
                    ->addLang(ISOLang::UR, 'کیا کوئی محفوظ شدہ کورس میری مہارتوں کی کمی سے مطابقت رکھتا ہے؟'),

                MUIKey::make('ai_assistant_prompt_saved_3')
                    ->addLang(ISOLang::EN, 'What should I learn next from my saved list?')
                    ->addLang(ISOLang::AR, 'ماذا ينبغي أن أتعلم بعد ذلك من قائمتي المحفوظة؟')
                    ->addLang(ISOLang::ES, '¿Qué debería aprender a continuación de mi lista guardada?')
                    ->addLang(ISOLang::FR, 'Que devrais-je apprendre ensuite dans ma liste enregistrée ?')
                    ->addLang(ISOLang::DE, 'Was sollte ich als Nächstes aus meiner gespeicherten Liste lernen?')
                    ->addLang(ISOLang::PT, 'O que devo aprender em seguida na minha lista salva?')
                    ->addLang(ISOLang::HI, 'मेरी सहेजी गई सूची से मुझे आगे क्या सीखना चाहिए?')
                    ->addLang(ISOLang::BN, 'আমার সংরক্ষিত তালিকা থেকে পরবর্তীতে কী শেখা উচিত?')
                    ->addLang(ISOLang::UR, 'میری محفوظ شدہ فہرست سے آگے کیا سیکھنا چاہیے؟'),

                // webinar
                MUIKey::make('ai_assistant_prompt_webinar_1')
                    ->addLang(ISOLang::EN, 'What webinars are coming up?')
                    ->addLang(ISOLang::AR, 'ما الندوات الافتراضية القادمة؟')
                    ->addLang(ISOLang::ES, '¿Qué seminarios web vienen próximamente?')
                    ->addLang(ISOLang::FR, 'Quels webinaires sont à venir ?')
                    ->addLang(ISOLang::DE, 'Welche Webinare stehen an?')
                    ->addLang(ISOLang::PT, 'Quais webinars estão próximos?')
                    ->addLang(ISOLang::HI, 'आगामी वेबिनार कौन-से हैं?')
                    ->addLang(ISOLang::BN, 'আসন্ন ওয়েবিনারগুলো কী কী?')
                    ->addLang(ISOLang::UR, 'آنے والے ویبینارز کون سے ہیں؟'),

                MUIKey::make('ai_assistant_prompt_webinar_2')
                    ->addLang(ISOLang::EN, 'Which webinars match my learning goals?')
                    ->addLang(ISOLang::AR, 'ما الندوات الافتراضية التي تتوافق مع أهداف تعلمي؟')
                    ->addLang(ISOLang::ES, '¿Qué seminarios web coinciden con mis objetivos?')
                    ->addLang(ISOLang::FR, 'Quels webinaires correspondent à mes objectifs ?')
                    ->addLang(ISOLang::DE, 'Welche Webinare passen zu meinen Lernzielen?')
                    ->addLang(ISOLang::PT, 'Quais webinars combinam com meus objetivos de aprendizagem?')
                    ->addLang(ISOLang::HI, 'कौन-से वेबिनार मेरे सीखने के लक्ष्यों से मेल खाते हैं?')
                    ->addLang(ISOLang::BN, 'কোন ওয়েবিনারগুলো আমার শেখার লক্ষ্যের সঙ্গে মিলে?')
                    ->addLang(ISOLang::UR, 'کون سے ویبینارز میرے تعلیمی اہداف سے مطابقت رکھتے ہیں؟'),

                MUIKey::make('ai_assistant_prompt_webinar_3')
                    ->addLang(ISOLang::EN, 'Are there webinars related to my skill gaps?')
                    ->addLang(ISOLang::AR, 'هل توجد ندوات افتراضية ذات صلة بالفجوات في مهاراتي؟')
                    ->addLang(ISOLang::ES, '¿Hay seminarios web relacionados con mis brechas de habilidades?')
                    ->addLang(ISOLang::FR, 'Y a-t-il des webinaires liés à mes lacunes ?')
                    ->addLang(ISOLang::DE, 'Gibt es Webinare zu meinen Fähigkeitslücken?')
                    ->addLang(ISOLang::PT, 'Existem webinars relacionados às minhas lacunas de habilidades?')
                    ->addLang(ISOLang::HI, 'क्या मेरे कौशल अंतराल से संबंधित वेबिनार हैं?')
                    ->addLang(ISOLang::BN, 'আমার দক্ষতার ঘাটতি সম্পর্কিত কি কোনো ওয়েবিনার আছে?')
                    ->addLang(ISOLang::UR, 'کیا میری مہارتوں کی کمی سے متعلق ویبینارز ہیں؟'),

                // calendar
                MUIKey::make('ai_assistant_prompt_calendar_1')
                    ->addLang(ISOLang::EN, 'When is my next event?')
                    ->addLang(ISOLang::AR, 'متى فعاليتي القادمة؟')
                    ->addLang(ISOLang::ES, '¿Cuándo es mi próximo evento?')
                    ->addLang(ISOLang::FR, 'Quand est mon prochain événement ?')
                    ->addLang(ISOLang::DE, 'Wann ist mein nächstes Ereignis?')
                    ->addLang(ISOLang::PT, 'Quando é meu próximo evento?')
                    ->addLang(ISOLang::HI, 'मेरी अगली इवेंट कब है?')
                    ->addLang(ISOLang::BN, 'আমার পরবর্তী ইভেন্ট কখন?')
                    ->addLang(ISOLang::UR, 'میرا اگلا ایونٹ کب ہے؟'),

                MUIKey::make('ai_assistant_prompt_calendar_2')
                    ->addLang(ISOLang::EN, 'What\'s in my upcoming access schedule?')
                    ->addLang(ISOLang::AR, 'ما الذي يتضمنه جدول الوصول القادم؟')
                    ->addLang(ISOLang::ES, '¿Qué hay en mi próximo calendario de acceso?')
                    ->addLang(ISOLang::FR, 'Qu\'y a-t-il dans mon prochain calendrier d\'accès ?')
                    ->addLang(ISOLang::DE, 'Was steht in meinem bevorstehenden Zugangsplan?')
                    ->addLang(ISOLang::PT, 'O que há no meu próximo cronograma de acesso?')
                    ->addLang(ISOLang::HI, 'मेरे आगामी एक्सेस शेड्यूल में क्या है?')
                    ->addLang(ISOLang::BN, 'আমার আসন্ন অ্যাক্সেস সময়সূচীতে কী আছে?')
                    ->addLang(ISOLang::UR, 'میرے آنے والے ایکسیس شیڈول میں کیا ہے؟'),

                MUIKey::make('ai_assistant_prompt_calendar_3')
                    ->addLang(ISOLang::EN, 'What\'s the difference between events and access schedule?')
                    ->addLang(ISOLang::AR, 'ما الفرق بين الفعاليات وجدول الوصول؟')
                    ->addLang(ISOLang::ES, '¿Cuál es la diferencia entre eventos y calendario de acceso?')
                    ->addLang(ISOLang::FR, 'Quelle est la différence entre événements et calendrier d\'accès ?')
                    ->addLang(ISOLang::DE, 'Was ist der Unterschied zwischen Ereignissen und Zugangsplan?')
                    ->addLang(ISOLang::PT, 'Qual é a diferença entre eventos e cronograma de acesso?')
                    ->addLang(ISOLang::HI, 'इवेंट और एक्सेस शेड्यूल में क्या अंतर है?')
                    ->addLang(ISOLang::BN, 'ইভেন্ট এবং অ্যাক্সেস সময়সূচীর মধ্যে পার্থক্য কী?')
                    ->addLang(ISOLang::UR, 'ایونٹس اور ایکسیس شیڈول میں کیا فرق ہے؟'),

                // faq
                MUIKey::make('ai_assistant_prompt_faq_1')
                    ->addLang(ISOLang::EN, 'How do I contact my admin?')
                    ->addLang(ISOLang::AR, 'كيف أتواصل مع المسؤول لديّ؟')
                    ->addLang(ISOLang::ES, '¿Cómo me contacto con mi administrador?')
                    ->addLang(ISOLang::FR, 'Comment contacter mon administrateur ?')
                    ->addLang(ISOLang::DE, 'Wie kontaktiere ich meinen Administrator?')
                    ->addLang(ISOLang::PT, 'Como entro em contato com meu administrador?')
                    ->addLang(ISOLang::HI, 'मैं अपने एडमिन से कैसे संपर्क करूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার অ্যাডমিনের সাথে যোগাযোগ করব?')
                    ->addLang(ISOLang::UR, 'میں اپنے ایڈمن سے کیسے رابطہ کروں؟'),

                MUIKey::make('ai_assistant_prompt_faq_2')
                    ->addLang(ISOLang::EN, 'What if I have a technical issue?')
                    ->addLang(ISOLang::AR, 'ماذا أفعل إذا واجهت مشكلة تقنية؟')
                    ->addLang(ISOLang::ES, '¿Qué hago si tengo un problema técnico?')
                    ->addLang(ISOLang::FR, 'Que faire si j\'ai un problème technique ?')
                    ->addLang(ISOLang::DE, 'Was tue ich bei einem technischen Problem?')
                    ->addLang(ISOLang::PT, 'E se eu tiver um problema técnico?')
                    ->addLang(ISOLang::HI, 'यदि मुझे तकनीकी समस्या हो तो क्या करूँ?')
                    ->addLang(ISOLang::BN, 'যদি আমার কোনো প্রযুক্তিগত সমস্যা হয় তাহলে কী করব?')
                    ->addLang(ISOLang::UR, 'اگر مجھے تکنیکی مسئلہ ہو تو کیا کروں؟'),

                MUIKey::make('ai_assistant_prompt_faq_3')
                    ->addLang(ISOLang::EN, 'How do tests and certificates work?')
                    ->addLang(ISOLang::AR, 'كيف تعمل الاختبارات والشهادات؟')
                    ->addLang(ISOLang::ES, '¿Cómo funcionan las pruebas y los certificados?')
                    ->addLang(ISOLang::FR, 'Comment fonctionnent les tests et les certificats ?')
                    ->addLang(ISOLang::DE, 'Wie funktionieren Tests und Zertifikate?')
                    ->addLang(ISOLang::PT, 'Como funcionam os testes e certificados?')
                    ->addLang(ISOLang::HI, 'परीक्षण और प्रमाणपत्र कैसे काम करते हैं?')
                    ->addLang(ISOLang::BN, 'পরীক্ষা এবং সার্টিফিকেট কীভাবে কাজ করে?')
                    ->addLang(ISOLang::UR, 'ٹیسٹ اور سرٹیفکیٹ کیسے کام کرتے ہیں؟'),

                // home
                MUIKey::make('ai_assistant_prompt_home_1')
                    ->addLang(ISOLang::EN, 'What should I work on today?')
                    ->addLang(ISOLang::AR, 'علامَ ينبغي أن أعمل اليوم؟')
                    ->addLang(ISOLang::ES, '¿En qué debería trabajar hoy?')
                    ->addLang(ISOLang::FR, 'Sur quoi devrais-je travailler aujourd\'hui ?')
                    ->addLang(ISOLang::DE, 'Woran sollte ich heute arbeiten?')
                    ->addLang(ISOLang::PT, 'Em que devo trabalhar hoje?')
                    ->addLang(ISOLang::HI, 'मुझे आज किस पर काम करना चाहिए?')
                    ->addLang(ISOLang::BN, 'আজ আমার কীসে কাজ করা উচিত?')
                    ->addLang(ISOLang::UR, 'مجھے آج کس چیز پر کام کرنا چاہیے؟'),

                MUIKey::make('ai_assistant_prompt_home_2')
                    ->addLang(ISOLang::EN, 'What skills do I need to develop?')
                    ->addLang(ISOLang::AR, 'ما المهارات التي أحتاج إلى تطويرها؟')
                    ->addLang(ISOLang::ES, '¿Qué habilidades necesito desarrollar?')
                    ->addLang(ISOLang::FR, 'Quelles compétences dois-je développer ?')
                    ->addLang(ISOLang::DE, 'Welche Fähigkeiten muss ich entwickeln?')
                    ->addLang(ISOLang::PT, 'Quais habilidades preciso desenvolver?')
                    ->addLang(ISOLang::HI, 'मुझे कौन-से कौशल विकसित करने की आवश्यकता है?')
                    ->addLang(ISOLang::BN, 'আমার কোন দক্ষতা গড়ে তোলা দরকার?')
                    ->addLang(ISOLang::UR, 'مجھے کون سی مہارتیں ترقی دینے کی ضرورت ہے؟'),

                MUIKey::make('ai_assistant_prompt_home_3')
                    ->addLang(ISOLang::EN, 'What are my upcoming deadlines?')
                    ->addLang(ISOLang::AR, 'ما هي المواعيد النهائية القادمة؟')
                    ->addLang(ISOLang::ES, '¿Cuáles son mis próximas fechas límite?')
                    ->addLang(ISOLang::FR, 'Quelles sont mes prochaines échéances ?')
                    ->addLang(ISOLang::DE, 'Was sind meine nächsten Fristen?')
                    ->addLang(ISOLang::PT, 'Quais são meus próximos prazos?')
                    ->addLang(ISOLang::HI, 'मेरी आगामी समय-सीमाएँ क्या हैं?')
                    ->addLang(ISOLang::BN, 'আমার আসন্ন সময়সীমা কী কী?')
                    ->addLang(ISOLang::UR, 'میری آنے والی ڈیڈ لائنز کیا ہیں؟'),

                // general
                MUIKey::make('ai_assistant_prompt_general_1')
                    ->addLang(ISOLang::EN, 'What skills should I focus on?')
                    ->addLang(ISOLang::AR, 'ما المهارات التي ينبغي أن أركز عليها؟')
                    ->addLang(ISOLang::ES, '¿En qué habilidades debería concentrarme?')
                    ->addLang(ISOLang::FR, 'Sur quelles compétences devrais-je me concentrer ?')
                    ->addLang(ISOLang::DE, 'Auf welche Fähigkeiten sollte ich mich konzentrieren?')
                    ->addLang(ISOLang::PT, 'Em quais habilidades devo focar?')
                    ->addLang(ISOLang::HI, 'मुझे किन कौशलों पर ध्यान केंद्रित करना चाहिए?')
                    ->addLang(ISOLang::BN, 'আমার কোন দক্ষতায় মনোনিবেশ করা উচিত?')
                    ->addLang(ISOLang::UR, 'مجھے کن مہارتوں پر توجہ دینی چاہیے؟'),

                MUIKey::make('ai_assistant_prompt_general_2')
                    ->addLang(ISOLang::EN, 'What are my upcoming deadlines?')
                    ->addLang(ISOLang::AR, 'ما هي المواعيد النهائية القادمة؟')
                    ->addLang(ISOLang::ES, '¿Cuáles son mis próximas fechas límite?')
                    ->addLang(ISOLang::FR, 'Quelles sont mes prochaines échéances ?')
                    ->addLang(ISOLang::DE, 'Was sind meine nächsten Fristen?')
                    ->addLang(ISOLang::PT, 'Quais são meus próximos prazos?')
                    ->addLang(ISOLang::HI, 'मेरी आगामी समय-सीमाएँ क्या हैं?')
                    ->addLang(ISOLang::BN, 'আমার আসন্ন সময়সীমা কী কী?')
                    ->addLang(ISOLang::UR, 'میری آنے والی ڈیڈ لائنز کیا ہیں؟'),

                MUIKey::make('ai_assistant_prompt_general_3')
                    ->addLang(ISOLang::EN, 'Help me create a learning plan')
                    ->addLang(ISOLang::AR, 'ساعدني في إنشاء خطة تعلم')
                    ->addLang(ISOLang::ES, 'Ayúdame a crear un plan de aprendizaje')
                    ->addLang(ISOLang::FR, 'Aidez-moi à créer un plan d\'apprentissage')
                    ->addLang(ISOLang::DE, 'Helfen Sie mir, einen Lernplan zu erstellen')
                    ->addLang(ISOLang::PT, 'Ajude-me a criar um plano de aprendizagem')
                    ->addLang(ISOLang::HI, 'सीखने की योजना बनाने में मेरी मदद करें')
                    ->addLang(ISOLang::BN, 'একটি শেখার পরিকল্পনা তৈরিতে সাহায্য করুন')
                    ->addLang(ISOLang::UR, 'سیکھنے کا منصوبہ بنانے میں میری مدد کریں'),

                // settings-personal
                MUIKey::make('ai_assistant_prompt_settings_personal_1')
                    ->addLang(ISOLang::EN, 'How do I change my time zone?')
                    ->addLang(ISOLang::AR, 'كيف أغير المنطقة الزمنية؟')
                    ->addLang(ISOLang::ES, '¿Cómo cambio mi zona horaria?')
                    ->addLang(ISOLang::FR, 'Comment changer mon fuseau horaire ?')
                    ->addLang(ISOLang::DE, 'Wie ändere ich meine Zeitzone?')
                    ->addLang(ISOLang::PT, 'Como altero meu fuso horário?')
                    ->addLang(ISOLang::HI, 'मैं अपना समय क्षेत्र कैसे बदलूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার সময় অঞ্চল পরিবর্তন করব?')
                    ->addLang(ISOLang::UR, 'میں اپنا ٹائم زون کیسے تبدیل کروں؟'),

                MUIKey::make('ai_assistant_prompt_settings_personal_2')
                    ->addLang(ISOLang::EN, 'How do I change my email language?')
                    ->addLang(ISOLang::AR, 'كيف أغير لغة البريد الإلكتروني؟')
                    ->addLang(ISOLang::ES, '¿Cómo cambio el idioma de mi correo?')
                    ->addLang(ISOLang::FR, 'Comment changer la langue de mes e-mails ?')
                    ->addLang(ISOLang::DE, 'Wie ändere ich die Sprache meiner E-Mails?')
                    ->addLang(ISOLang::PT, 'Como altero o idioma do meu e-mail?')
                    ->addLang(ISOLang::HI, 'मैं अपने ईमेल की भाषा कैसे बदलूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার ইমেল ভাষা পরিবর্তন করব?')
                    ->addLang(ISOLang::UR, 'میں اپنی ای میل زبان کیسے تبدیل کروں؟'),

                MUIKey::make('ai_assistant_prompt_settings_personal_3')
                    ->addLang(ISOLang::EN, 'How do I update my profile photo?')
                    ->addLang(ISOLang::AR, 'كيف أحدث صورة الملف الشخصي؟')
                    ->addLang(ISOLang::ES, '¿Cómo actualizo mi foto de perfil?')
                    ->addLang(ISOLang::FR, 'Comment mettre à jour ma photo de profil ?')
                    ->addLang(ISOLang::DE, 'Wie aktualisiere ich mein Profilbild?')
                    ->addLang(ISOLang::PT, 'Como atualizo minha foto de perfil?')
                    ->addLang(ISOLang::HI, 'मैं अपनी प्रोफ़ाइल फ़ोटो कैसे अपडेट करूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার প্রোফাইল ফটো আপডেট করব?')
                    ->addLang(ISOLang::UR, 'میں اپنی پروفائل تصویر کیسے اپڈیٹ کروں؟'),

                // settings-privacy
                MUIKey::make('ai_assistant_prompt_settings_privacy_1')
                    ->addLang(ISOLang::EN, 'How do I change my password?')
                    ->addLang(ISOLang::AR, 'كيف أغير كلمة المرور؟')
                    ->addLang(ISOLang::ES, '¿Cómo cambio mi contraseña?')
                    ->addLang(ISOLang::FR, 'Comment changer mon mot de passe ?')
                    ->addLang(ISOLang::DE, 'Wie ändere ich mein Passwort?')
                    ->addLang(ISOLang::PT, 'Como altero minha senha?')
                    ->addLang(ISOLang::HI, 'मैं अपना पासवर्ड कैसे बदलूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার পাসওয়ার্ড পরিবর্তন করব?')
                    ->addLang(ISOLang::UR, 'میں اپنا پاس ورڈ کیسے تبدیل کروں؟'),

                MUIKey::make('ai_assistant_prompt_settings_privacy_2')
                    ->addLang(ISOLang::EN, 'What is PIN security for?')
                    ->addLang(ISOLang::AR, 'ما الغرض من أمان رقم التعريف الشخصي (PIN)؟')
                    ->addLang(ISOLang::ES, '¿Para qué sirve la seguridad por PIN?')
                    ->addLang(ISOLang::FR, 'À quoi sert la sécurité par code PIN ?')
                    ->addLang(ISOLang::DE, 'Wofür dient die PIN-Sicherheit?')
                    ->addLang(ISOLang::PT, 'Para que serve a segurança por PIN?')
                    ->addLang(ISOLang::HI, 'पिन सुरक्षा किसलिए होती है?')
                    ->addLang(ISOLang::BN, 'পিন নিরাপত্তা কীসের জন্য?')
                    ->addLang(ISOLang::UR, 'پن سیکیورٹی کس لیے ہے؟'),

                MUIKey::make('ai_assistant_prompt_settings_privacy_3')
                    ->addLang(ISOLang::EN, 'How do I keep my account secure?')
                    ->addLang(ISOLang::AR, 'كيف أحافظ على أمان حسابي؟')
                    ->addLang(ISOLang::ES, '¿Cómo mantengo mi cuenta segura?')
                    ->addLang(ISOLang::FR, 'Comment sécuriser mon compte ?')
                    ->addLang(ISOLang::DE, 'Wie halte ich mein Konto sicher?')
                    ->addLang(ISOLang::PT, 'Como mantenho minha conta segura?')
                    ->addLang(ISOLang::HI, 'मैं अपने खाते को सुरक्षित कैसे रखूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার অ্যাকাউন্ট নিরাপদ রাখব?')
                    ->addLang(ISOLang::UR, 'میں اپنے اکاؤنٹ کو محفوظ کیسے رکھوں؟'),

                // settings-license
                MUIKey::make('ai_assistant_prompt_settings_license_1')
                    ->addLang(ISOLang::EN, 'What is a subscription?')
                    ->addLang(ISOLang::AR, 'ما الاشتراك؟')
                    ->addLang(ISOLang::ES, '¿Qué es una suscripción?')
                    ->addLang(ISOLang::FR, 'Qu\'est-ce qu\'un abonnement ?')
                    ->addLang(ISOLang::DE, 'Was ist ein Abonnement?')
                    ->addLang(ISOLang::PT, 'O que é uma assinatura?')
                    ->addLang(ISOLang::HI, 'सदस्यता क्या है?')
                    ->addLang(ISOLang::BN, 'সাবস্ক্রিপশন কী?')
                    ->addLang(ISOLang::UR, 'سبسکرپشن کیا ہے؟'),

                MUIKey::make('ai_assistant_prompt_settings_license_2')
                    ->addLang(ISOLang::EN, 'Who do I contact about my license?')
                    ->addLang(ISOLang::AR, 'بمن أتواصل بخصوص الترخيص الخاص بي؟')
                    ->addLang(ISOLang::ES, '¿Con quién me pongo en contacto sobre mi licencia?')
                    ->addLang(ISOLang::FR, 'Qui dois-je contacter concernant ma licence ?')
                    ->addLang(ISOLang::DE, 'Wen kontaktiere ich bezüglich meiner Lizenz?')
                    ->addLang(ISOLang::PT, 'Com quem entro em contato sobre minha licença?')
                    ->addLang(ISOLang::HI, 'मैं अपने लाइसेंस के बारे में किससे संपर्क करूँ?')
                    ->addLang(ISOLang::BN, 'আমার লাইসেন্স সম্পর্কে আমি কার সাথে যোগাযোগ করব?')
                    ->addLang(ISOLang::UR, 'میں اپنے لائسنس کے بارے میں کس سے رابطہ کروں؟'),

                MUIKey::make('ai_assistant_prompt_settings_license_3')
                    ->addLang(ISOLang::EN, 'How do I know my subscription is active?')
                    ->addLang(ISOLang::AR, 'كيف أعرف أن اشتراكي نشط؟')
                    ->addLang(ISOLang::ES, '¿Cómo sé si mi suscripción está activa?')
                    ->addLang(ISOLang::FR, 'Comment savoir si mon abonnement est actif ?')
                    ->addLang(ISOLang::DE, 'Woran erkenne ich, dass mein Abonnement aktiv ist?')
                    ->addLang(ISOLang::PT, 'Como sei se minha assinatura está ativa?')
                    ->addLang(ISOLang::HI, 'मुझे कैसे पता चलेगा कि मेरी सदस्यता सक्रिय है?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে জানব যে আমার সাবস্ক্রিপশন সক্রিয়?')
                    ->addLang(ISOLang::UR, 'مجھے کیسے پتا چلے گا کہ میری سبسکرپشن فعال ہے؟'),

                // settings (generic fallback)
                MUIKey::make('ai_assistant_prompt_settings_1')
                    ->addLang(ISOLang::EN, 'How do I change my password?')
                    ->addLang(ISOLang::AR, 'كيف أغير كلمة المرور؟')
                    ->addLang(ISOLang::ES, '¿Cómo cambio mi contraseña?')
                    ->addLang(ISOLang::FR, 'Comment changer mon mot de passe ?')
                    ->addLang(ISOLang::DE, 'Wie ändere ich mein Passwort?')
                    ->addLang(ISOLang::PT, 'Como altero minha senha?')
                    ->addLang(ISOLang::HI, 'मैं अपना पासवर्ड कैसे बदलूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার পাসওয়ার্ড পরিবর্তন করব?')
                    ->addLang(ISOLang::UR, 'میں اپنا پاس ورڈ کیسے تبدیل کروں؟'),

                MUIKey::make('ai_assistant_prompt_settings_2')
                    ->addLang(ISOLang::EN, 'How do I change my time zone?')
                    ->addLang(ISOLang::AR, 'كيف أغير المنطقة الزمنية؟')
                    ->addLang(ISOLang::ES, '¿Cómo cambio mi zona horaria?')
                    ->addLang(ISOLang::FR, 'Comment changer mon fuseau horaire ?')
                    ->addLang(ISOLang::DE, 'Wie ändere ich meine Zeitzone?')
                    ->addLang(ISOLang::PT, 'Como altero meu fuso horário?')
                    ->addLang(ISOLang::HI, 'मैं अपना समय क्षेत्र कैसे बदलूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার সময় অঞ্চল পরিবর্তন করব?')
                    ->addLang(ISOLang::UR, 'میں اپنا ٹائم زون کیسے تبدیل کروں؟'),

                MUIKey::make('ai_assistant_prompt_settings_3')
                    ->addLang(ISOLang::EN, 'How do I keep my account secure?')
                    ->addLang(ISOLang::AR, 'كيف أحافظ على أمان حسابي؟')
                    ->addLang(ISOLang::ES, '¿Cómo mantengo mi cuenta segura?')
                    ->addLang(ISOLang::FR, 'Comment sécuriser mon compte ?')
                    ->addLang(ISOLang::DE, 'Wie halte ich mein Konto sicher?')
                    ->addLang(ISOLang::PT, 'Como mantenho minha conta segura?')
                    ->addLang(ISOLang::HI, 'मैं अपने खाते को सुरक्षित कैसे रखूँ?')
                    ->addLang(ISOLang::BN, 'আমি কীভাবে আমার অ্যাকাউন্ট নিরাপদ রাখব?')
                    ->addLang(ISOLang::UR, 'میں اپنے اکاؤنٹ کو محفوظ کیسے رکھوں؟'),

                // ── Greeting response template (QA-NEW-15) ───────────────
                // Backend reads this by $lang, substitutes {page_name} with
                // the localized page-context label (ai_assistant_context_*),
                // and inlines the resolved sentence into the system prompt
                // as the GREETINGS-rule literal response.
                MUIKey::make('ai_assistant_greeting_response')
                    ->addLang(ISOLang::EN, 'Welcome to KnowledgeCity. I\'m here to help you with your learning journey. You\'re currently on the {page_name} page. What would you like to focus on today — checking your assignments, exploring new courses, or something else?')
                    ->addLang(ISOLang::AR, 'مرحبًا بك في KnowledgeCity. أنا هنا لمساعدتك في رحلتك التعليمية. أنت حاليًا على صفحة {page_name}. على ماذا تودّ التركيز اليوم — مراجعة مهامك، استكشاف دورات جديدة، أو شيء آخر؟')
                    ->addLang(ISOLang::ES, 'Bienvenido a KnowledgeCity. Estoy aquí para ayudarte en tu recorrido de aprendizaje. Actualmente estás en la página {page_name}. ¿En qué te gustaría centrarte hoy — revisar tus tareas, explorar cursos nuevos, u otra cosa?')
                    ->addLang(ISOLang::FR, 'Bienvenue chez KnowledgeCity. Je suis là pour vous accompagner dans votre parcours d\'apprentissage. Vous êtes actuellement sur la page {page_name}. Sur quoi souhaitez-vous vous concentrer aujourd\'hui — consulter vos devoirs, explorer de nouveaux cours, ou autre chose ?')
                    ->addLang(ISOLang::DE, 'Willkommen bei KnowledgeCity. Ich bin hier, um Sie auf Ihrer Lernreise zu unterstützen. Sie befinden sich derzeit auf der Seite {page_name}. Worauf möchten Sie sich heute konzentrieren — Aufgaben prüfen, neue Kurse erkunden oder etwas anderes?')
                    ->addLang(ISOLang::PT, 'Bem-vindo ao KnowledgeCity. Estou aqui para ajudá-lo na sua jornada de aprendizagem. Atualmente você está na página {page_name}. Em que gostaria de se concentrar hoje — verificar as suas tarefas, explorar novos cursos ou outra coisa?')
                    ->addLang(ISOLang::HI, 'KnowledgeCity में आपका स्वागत है। मैं आपकी सीखने की यात्रा में आपकी मदद के लिए यहाँ हूँ। आप अभी {page_name} पृष्ठ पर हैं। आज आप किस पर ध्यान देना चाहेंगे — अपने कार्य देखना, नए कोर्स खोजना, या कुछ और?')
                    ->addLang(ISOLang::BN, 'KnowledgeCity-এ স্বাগতম। আপনার শেখার যাত্রায় সাহায্য করতে আমি এখানে আছি। আপনি বর্তমানে {page_name} পৃষ্ঠায় আছেন। আজ আপনি কীসে মনোযোগ দিতে চান — আপনার অ্যাসাইনমেন্ট দেখা, নতুন কোর্স অন্বেষণ করা, নাকি অন্য কিছু?')
                    ->addLang(ISOLang::UR, 'KnowledgeCity میں خوش آمدید۔ میں آپ کے سیکھنے کے سفر میں مدد کے لیے یہاں ہوں۔ آپ ابھی {page_name} صفحے پر ہیں۔ آج آپ کس چیز پر توجہ دینا چاہیں گے — اپنے اسائنمنٹس دیکھنا، نئے کورسز دریافت کرنا، یا کچھ اور؟'),

                // ── Honest-fallback template (QA-NEW-18) ─────────────────
                // Returned verbatim by the AI when the user asks for data
                // that isn't in the system prompt (per HONESTY RULE). Was
                // hardcoded EN; now resolved by $lang server-side before
                // being inlined into the system prompt literal.
                MUIKey::make('ai_assistant_honest_fallback')
                    ->addLang(ISOLang::EN, 'I don\'t have that information yet. Please check the relevant page of the portal, or contact your administrator.')
                    ->addLang(ISOLang::AR, 'ليست لديّ هذه المعلومات بعد. يُرجى مراجعة الصفحة المناسبة في البوابة، أو التواصل مع المسؤول.')
                    ->addLang(ISOLang::ES, 'Aún no dispongo de esa información. Consulta la página correspondiente del portal o contacta con tu administrador.')
                    ->addLang(ISOLang::FR, 'Je n\'ai pas encore cette information. Veuillez consulter la page correspondante du portail ou contacter votre administrateur.')
                    ->addLang(ISOLang::DE, 'Diese Information habe ich noch nicht. Bitte prüfen Sie die entsprechende Seite im Portal oder wenden Sie sich an Ihren Administrator.')
                    ->addLang(ISOLang::PT, 'Ainda não tenho essa informação. Consulte a página correspondente do portal ou contacte o seu administrador.')
                    ->addLang(ISOLang::HI, 'मेरे पास अभी वह जानकारी नहीं है। कृपया पोर्टल के संबंधित पृष्ठ को देखें, या अपने व्यवस्थापक से संपर्क करें।')
                    ->addLang(ISOLang::BN, 'আমার কাছে এখনো সেই তথ্য নেই। অনুগ্রহ করে পোর্টালের প্রাসঙ্গিক পৃষ্ঠা দেখুন, অথবা আপনার প্রশাসকের সাথে যোগাযোগ করুন।')
                    ->addLang(ISOLang::UR, 'میرے پاس ابھی وہ معلومات نہیں ہیں۔ براہِ کرم پورٹل کا متعلقہ صفحہ دیکھیں، یا اپنے منتظم سے رابطہ کریں۔'),

                // ── Confidentiality refusal template (QA-15) ─────────────
                // Emitted verbatim by the AI when a prompt-injection attempt
                // is detected (see CONFIDENTIALITY rule). Was hardcoded EN;
                // resolved server-side by $lang before inlining into the
                // system prompt literal.
                MUIKey::make('ai_assistant_refusal_template')
                    ->addLang(ISOLang::EN, 'I can\'t share my internal instructions. I\'m here to help you with your learning — ask me about your assignments, courses, or the platform.')
                    ->addLang(ISOLang::AR, 'لا يمكنني مشاركة تعليماتي الداخلية. أنا هنا لمساعدتك في تعلّمك — اسألني عن مهامك أو دوراتك أو المنصة.')
                    ->addLang(ISOLang::ES, 'No puedo compartir mis instrucciones internas. Estoy aquí para ayudarte con tu aprendizaje — pregúntame sobre tus tareas, cursos o la plataforma.')
                    ->addLang(ISOLang::FR, 'Je ne peux pas partager mes instructions internes. Je suis là pour vous aider dans votre apprentissage — posez-moi des questions sur vos devoirs, vos cours ou la plateforme.')
                    ->addLang(ISOLang::DE, 'Ich kann meine internen Anweisungen nicht weitergeben. Ich bin hier, um Sie beim Lernen zu unterstützen — fragen Sie mich zu Ihren Aufgaben, Kursen oder zur Plattform.')
                    ->addLang(ISOLang::PT, 'Não posso compartilhar minhas instruções internas. Estou aqui para ajudá-lo com a sua aprendizagem — pergunte-me sobre as suas tarefas, cursos ou a plataforma.')
                    ->addLang(ISOLang::HI, 'मैं अपने आंतरिक निर्देश साझा नहीं कर सकता। मैं आपकी सीखने में मदद के लिए यहाँ हूँ — मुझसे अपने कार्यों, कोर्सों या प्लेटफ़ॉर्म के बारे में पूछें।')
                    ->addLang(ISOLang::BN, 'আমি আমার অভ্যন্তরীণ নির্দেশাবলী শেয়ার করতে পারি না। আমি আপনার শেখার জন্য সাহায্য করতে এখানে আছি — আমাকে আপনার অ্যাসাইনমেন্ট, কোর্স বা প্ল্যাটফর্ম সম্পর্কে জিজ্ঞাসা করুন।')
                    ->addLang(ISOLang::UR, 'میں اپنی اندرونی ہدایات شیئر نہیں کر سکتا۔ میں آپ کے سیکھنے میں مدد کے لیے یہاں ہوں — مجھ سے اپنے اسائنمنٹس، کورسز یا پلیٹ فارم کے بارے میں پوچھیں۔'),
            );
    }
};
