document.addEventListener('DOMContentLoaded', function() {
    const reponseTextarea = document.getElementById('reponse');
    const charCount = document.getElementById('charCount');
    const charLimit = document.getElementById('charLimit');
    const previewContent = document.getElementById('previewContent');
    const previewBtn = document.getElementById('previewBtn');
    const previewArea = document.getElementById('previewArea');
    const MAX_CHARS = 2000;
    
    // Initialiser le compteur
    updateCharCount();
    
    // Mettre à jour le compteur de caractères
    function updateCharCount() {
        const count = reponseTextarea.value.length;
        charCount.textContent = count;
        
        // Changer la couleur selon la longueur
        if (count > MAX_CHARS) {
            charCount.style.color = '#ff6b6b';
            charLimit.style.color = '#ff6b6b';
            charLimit.textContent = '(Limite dépassée !)';
        } else if (count > MAX_CHARS * 0.8) {
            charCount.style.color = '#ffb347';
            charLimit.style.color = '#ffb347';
            charLimit.textContent = `(Limite: ${MAX_CHARS} caractères - Attention !)`;
        } else {
            charCount.style.color = '#4aff8b';
            charLimit.style.color = '#666';
            charLimit.textContent = `(Limite: ${MAX_CHARS} caractères)`;
        }
    }
    
    // Mettre à jour l'aperçu
    function updatePreview() {
        const content = reponseTextarea.value.trim();
        if (content) {
            // Convertir les sauts de ligne en balises <br> et échapper le HTML
            const formattedContent = content
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\n/g, '<br>');
            previewContent.innerHTML = formattedContent;
        } else {
            previewContent.innerHTML = '<em>Aucun contenu à prévisualiser...</em>';
        }
    }
    
    // Toggle l'affichage de l'aperçu
    function togglePreview() {
        updatePreview();
        previewArea.style.display = previewArea.style.display === 'none' ? 'block' : 'none';
        previewBtn.innerHTML = previewArea.style.display === 'none' 
            ? '<i class="bi bi-eye"></i> Aperçu' 
            : '<i class="bi bi-eye-slash"></i> Cacher l\'aperçu';
    }
    
    // Valider avant soumission
    function validateForm(event) {
        const count = reponseTextarea.value.length;
        if (count > MAX_CHARS) {
            event.preventDefault();
            alert(`Votre message dépasse la limite de ${MAX_CHARS} caractères. Veuillez le raccourcir.`);
            return false;
        }
        if (count === 0) {
            event.preventDefault();
            alert("Le message de réponse ne peut pas être vide.");
            return false;
        }
        return true;
    }
    
    // Événements pour les fonctions de base
    reponseTextarea.addEventListener('input', updateCharCount);
    previewBtn.addEventListener('click', togglePreview);
    
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', validateForm);
    
    // Empêcher la saisie au-delà de la limite
    reponseTextarea.addEventListener('keypress', function(e) {
        if (this.value.length >= MAX_CHARS) {
            e.preventDefault();
        }
    });
    
    // ============================
    // FONCTIONNALITÉS D'IA
    // ============================
    
    // Éléments d'IA
    const aiGenerateBtn = document.getElementById('aiGenerate');
    const aiImproveBtn = document.getElementById('aiImprove');
    const aiTranslateBtn = document.getElementById('aiTranslate');
    const aiToneBtn = document.getElementById('aiTone');
    const toneOptions = document.getElementById('toneOptions');
    const languageOptions = document.getElementById('languageOptions');
    const aiLoading = document.getElementById('aiLoading');
    
    // Données de la réclamation pour le contexte de l'IA
    const reclamationData = {
        sujet: document.querySelector('input[name="reclamation_sujet"]')?.value || "Réclamation",
        description: document.querySelector('input[name="reclamation_description"]')?.value || "Description de la réclamation",
        statut: document.querySelector('input[name="reclamation_statut"]')?.value || "en attente",
        user: document.querySelector('input[name="reclamation_user"]')?.value || "Utilisateur"
    };
    
    // Afficher/cacher les options de ton
    if (aiToneBtn) {
        aiToneBtn.addEventListener('click', function() {
            toneOptions.style.display = toneOptions.style.display === 'none' ? 'flex' : 'none';
            languageOptions.style.display = 'none';
        });
    }
    
    // Afficher/cacher les options de langue
    if (aiTranslateBtn) {
        aiTranslateBtn.addEventListener('click', function() {
            languageOptions.style.display = languageOptions.style.display === 'none' ? 'flex' : 'none';
            toneOptions.style.display = 'none';
        });
    }
    
    // Générer une réponse avec IA
    if (aiGenerateBtn) {
        aiGenerateBtn.addEventListener('click', function() {
            generateAIResponse('generate');
        });
    }
    
    // Améliorer le texte existant
    if (aiImproveBtn) {
        aiImproveBtn.addEventListener('click', function() {
            if (reponseTextarea.value.trim() === '') {
                showToast('Veuillez d\'abord écrire quelque chose à améliorer.');
                return;
            }
            generateAIResponse('improve');
        });
    }
    
    // Gérer les options de ton
    if (toneOptions) {
        toneOptions.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON') {
                const tone = e.target.dataset.tone;
                // Retirer la classe active de tous les boutons
                toneOptions.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('active');
                });
                // Ajouter la classe active au bouton cliqué
                e.target.classList.add('active');
                generateAIResponse('tone', tone);
            }
        });
    }
    
    // Gérer les options de langue
    if (languageOptions) {
        languageOptions.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON') {
                const lang = e.target.dataset.lang;
                languageOptions.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('active');
                });
                e.target.classList.add('active');
                generateAIResponse('translate', lang);
            }
        });
    }
    
    // Fonction principale pour appeler l'IA
    async function generateAIResponse(action, parameter = null) {
        const currentText = reponseTextarea.value;
        
        // Afficher l'indicateur de chargement
        aiLoading.style.display = 'flex';
        
        try {
            let prompt = '';
            
            switch(action) {
                case 'generate':
                    prompt = `En tant qu'administrateur de support, génère une réponse professionnelle pour une réclamation.
                             Sujet: ${reclamationData.sujet}
                             Description du problème: ${reclamationData.description}
                             Statut actuel: ${reclamationData.statut}
                             Utilisateur: ${reclamationData.user}
                             
                             La réponse doit être:
                             - Empathique et professionnelle
                             - Proposer une solution ou des étapes de résolution
                             - Inclure une estimation de temps si possible
                             - Se terminer par une formule de politesse`;
                    break;
                    
                case 'improve':
                    prompt = `Améliore ce texte de réponse à un client:
                             Texte actuel: ${currentText}
                             
                             Améliorations à apporter:
                             1. Rendre plus professionnel
                             2. Clarifier le message
                             3. Ajouter de l'empathie
                             4. Corriger les fautes de grammaire`;
                    break;
                    
                case 'tone':
                    prompt = `Réécris ce texte avec un ton ${parameter}:
                             Texte: ${currentText}
                             
                             Règles pour le ton ${parameter}:
                             ${getToneRules(parameter)}`;
                    break;
                    
                case 'translate':
                    prompt = `Traduis ce texte en ${getLanguageName(parameter)}:
                             Texte: ${currentText}
                             
                             La traduction doit:
                             - Garder le sens original
                             - Être naturelle dans la langue cible
                             - Conserver le ton professionnel`;
                    break;
            }
            
            // Appel à l'API IA
            const aiResponse = await simulateAIAPI(prompt, action, currentText, parameter);
            
            // Afficher la suggestion
            displayAISuggestion(aiResponse, action, parameter);
            
        } catch (error) {
            showToast('Erreur lors de la génération: ' + error.message, 'error');
        } finally {
            // Cacher l'indicateur de chargement
            aiLoading.style.display = 'none';
        }
    }
    
    // Fonction de simulation d'API IA
    function simulateAIAPI(prompt, action, currentText, parameter) {
        return new Promise((resolve) => {
            setTimeout(() => {
                let response;
                
                switch(action) {
                    case 'generate':
                        response = `Cher ${reclamationData.user},

Nous avons bien reçu votre réclamation concernant "${reclamationData.sujet}".

${getResponseForSujet(reclamationData.sujet)}

Nous traitons actuellement votre demande avec la plus grande attention. Notre équipe technique examine le problème et vous tiendra informé de l'avancement.

Temps de traitement estimé : 24-48 heures

N'hésitez pas à nous contacter si vous avez d'autres questions.

Cordialement,
L'équipe de support Tunispace`;
                        break;
                        
                    case 'improve':
                        response = `Version améliorée de votre texte:

${currentText}

${getImprovementSuggestions(currentText)}`;
                        break;
                        
                    case 'tone':
                        response = `Version avec ton ${parameter} appliqué:

${currentText}

Le ton a été adapté pour être plus ${parameter} tout en conservant votre message principal.`;
                        break;
                        
                    case 'translate':
                        response = `Traduction en ${getLanguageName(parameter)}:

${getTranslationExample(currentText, parameter)}`;
                        break;
                        
                    default:
                        response = "Réponse générée par l'IA.";
                }
                
                resolve(response);
            }, 1500);
        });
    }
    
    // Afficher la suggestion d'IA
    function displayAISuggestion(content, action, parameter) {
        // Supprimer toute suggestion précédente
        const existingSuggestion = document.querySelector('.ai-suggestion');
        if (existingSuggestion) {
            existingSuggestion.remove();
        }
        
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'ai-suggestion';
        
        const title = getSuggestionTitle(action, parameter);
        
        suggestionDiv.innerHTML = `
            <div class="ai-suggestion-header">
                <h5><i class="bi bi-robot"></i> ${title}</h5>
                <div class="ai-suggestion-actions">
                    <button class="btn-add btn-use-suggestion" data-action="replace">Remplacer</button>
                    <button class="btn-add btn-use-suggestion" data-action="insert">Insérer à la fin</button>
                    <button class="btn-add btn-use-suggestion" data-action="discard">Ignorer</button>
                </div>
            </div>
            <div class="ai-suggestion-content">${escapeHtml(content)}</div>
        `;
        
        // Insérer après le textarea
        reponseTextarea.parentNode.insertBefore(suggestionDiv, reponseTextarea.nextSibling);
        
        // Gérer les actions sur la suggestion
        suggestionDiv.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-use-suggestion')) {
                const actionType = e.target.dataset.action;
                
                switch(actionType) {
                    case 'replace':
                        reponseTextarea.value = content;
                        showToast('Réponse remplacée avec succès !');
                        break;
                    case 'insert':
                        const current = reponseTextarea.value;
                        if (current.trim() === '') {
                            reponseTextarea.value = content;
                        } else {
                            reponseTextarea.value = current + '\n\n' + content;
                        }
                        showToast('Réponse insérée avec succès !');
                        break;
                    case 'discard':
                        // Ne rien faire
                        break;
                }
                
                // Supprimer la suggestion
                suggestionDiv.remove();
                updateCharCount();
            }
        });
    }
    
    // ============================
    // FONCTIONS UTILITAIRES
    // ============================
    
    function getResponseForSujet(sujet) {
        const responses = {
            'Contenu incorrect': 'Nous vérifions actuellement le contenu signalé et prendrons les mesures appropriées dans les plus brefs délais.',
            'Probleme technique': 'Notre équipe technique a été informée et travaille à résoudre ce problème technique.',
            'probleme de securite': 'La sécurité étant notre priorité, nous enquêtons immédiatement sur cette préoccupation.',
            'Compte bloqué': 'Nous examinons les raisons du blocage de votre compte et vous contacterons pour plus d\'informations.'
        };
        
        return responses[sujet] || 'Nous traitons votre demande avec la plus grande attention.';
    }
    
    function getImprovementSuggestions(text) {
        return "✅ Améliorations apportées :\n• Structure clarifiée\n• Ton professionnel ajusté\n• Grammaire et orthographe vérifiées\n• Phrases reformulées pour plus de clarté";
    }
    
    function getToneRules(tone) {
        const rules = {
            'professionnel': '- Utiliser un langage formel\n- Éviter les contractions\n- Être direct et précis',
            'amical': '- Utiliser un ton chaleureux\n- Inclure des salutations personnalisées\n- Être encourageant',
            'empathique': '- Reconnaître les émotions\n- Montrer de la compréhension\n- Être rassurant',
            'formel': '- Utiliser le vouvoiement\n- Structure formelle\n- Langage soutenu',
            'simple': '- Phrases courtes et claires\n- Langage accessible\n- Points précis'
        };
        
        return rules[tone] || '';
    }
    
    function getLanguageName(code) {
        const languages = {
            'fr': 'français',
            'en': 'anglais',
            'ar': 'arabe',
            'es': 'espagnol',
            'de': 'allemand'
        };
        
        return languages[code] || code;
    }
    
    function getTranslationExample(text, lang) {
        // Exemples de traductions
        if (lang === 'en') {
            return "Dear " + reclamationData.user + ",\n\nThank you for contacting us regarding your issue.\nWe are currently investigating the matter and will get back to you as soon as possible.\n\nBest regards,\nTunispace Support Team";
        } else if (lang === 'ar') {
            return "عزيزي " + reclamationData.user + "،\n\nشكرًا لتواصلك معنا بشأن مشكلتك.\nنحن نتحقق من الأمر حالياً وسنعود إليك في أقرب وقت ممكن.\n\nمع خالص التقدير،\nفريق دعم Tunispace";
        } else if (lang === 'fr') {
            return text || "Cher " + reclamationData.user + ",\n\nNous avons bien reçu votre réclamation et la traitons avec attention.";
        }
        return text + "\n\n[Traduction en " + getLanguageName(lang) + "]";
    }
    
    function getSuggestionTitle(action, parameter) {
        const titles = {
            'generate': '✨ Suggestion de réponse IA',
            'improve': '⚡ Texte amélioré par IA',
            'tone': `🎭 Ton ${parameter} appliqué`,
            'translate': `🌍 Traduction en ${getLanguageName(parameter)}`
        };
        
        return titles[action] || '🤖 Suggestion IA';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function showToast(message, type = 'success') {
        // Supprimer les toasts existants
        const existingToasts = document.querySelectorAll('.ai-toast');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `ai-toast ${type}`;
        toast.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Positionner le toast
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '8px';
        toast.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
        toast.style.color = 'white';
        toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        toast.style.zIndex = '10000';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.fontSize = '14px';
        
        // Animation d'entrée
        toast.style.animation = 'slideInRight 0.3s ease-out';
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 3000);
        
        // Ajouter les animations CSS si elles n'existent pas
        if (!document.querySelector('#toast-animations')) {
            const style = document.createElement('style');
            style.id = 'toast-animations';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Ajouter du style aux boutons de suggestion
    function styleSuggestionButtons() {
        const style = document.createElement('style');
        style.textContent = `
            .ai-suggestion {
                margin-top: 15px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #4aff8b;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            .ai-suggestion-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            .ai-suggestion-header h5 {
                margin: 0;
                color: #333;
                font-size: 16px;
            }
            .ai-suggestion-actions {
                display: flex;
                gap: 10px;
            }
            .ai-suggestion-actions .btn-use-suggestion {
                padding: 6px 12px;
                font-size: 14px;
                cursor: pointer;
                border: none;
                border-radius: 4px;
                transition: all 0.2s;
            }
            .ai-suggestion-actions .btn-use-suggestion:hover {
                opacity: 0.9;
                transform: translateY(-1px);
            }
            .ai-suggestion-actions .btn-use-suggestion[data-action="replace"] {
                background: #4CAF50;
                color: white;
            }
            .ai-suggestion-actions .btn-use-suggestion[data-action="insert"] {
                background: #2196F3;
                color: white;
            }
            .ai-suggestion-actions .btn-use-suggestion[data-action="discard"] {
                background: #f44336;
                color: white;
            }
            .ai-suggestion-content {
                white-space: pre-wrap;
                padding: 10px;
                background: white;
                border-radius: 6px;
                border: 1px solid #e0e0e0;
                font-family: 'Inter', sans-serif;
                font-size: 14px;
                line-height: 1.5;
                max-height: 300px;
                overflow-y: auto;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Appeler la fonction de style au chargement
    styleSuggestionButtons();
    
    // Initialiser le compteur de caractères
    updateCharCount();
});