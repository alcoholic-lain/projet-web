<?php
include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../controller/ReclamationController.php';

$error = "";
$success = "";

if($_POST) {
    $user = "Utilisateur Anonyme";
    $sujet = $_POST['sujet_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';
    
    // Validation
    $errors = validateReclamation($user, $sujet, $description);
    
    if(empty($errors)) {
        $controller = new ReclamationController();
        if($controller->addReclamation($user, $sujet, $description, $statut)) {
            $success = "RÃ©clamation ajoutÃ©e avec succÃ¨s!";
            $_POST = array();
        } else {
            $error = "Erreur lors de l'ajout de la rÃ©clamation";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une RÃ©clamation</title>
    <link rel="stylesheet" href="add.css">
    
 
</head>
<body>

<header>
    <h1>• Ajouter une Reclamation</h1>
</header>

<main>
    <div class="text-center">
        <h2>Nouvelle reclamation</h2>
    </div>

    <!-- Messages d'erreur et de succÃ¨s -->
        
    <?php if($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- ===== FORMULAIRE DE CONTACT ===== -->
    <section class="form-container">

        <!-- === ZONE DE 4 IMAGES === -->
        <div class="image-grid">
            <div class="img-box" data-type="Contenu incorrect">
                <label>Contenu incorrect</label>
                <img src="Contenu incorrect.jpg" alt="Contenu_incorrect">
            </div>
            <div class="img-box" data-type="ProblÃ¨me technique">
                <label>Problème technique</label>
                <img src="Problème technique.jpg" alt="ProblÃ¨me_technique">
            </div>
            <div class="img-box" data-type="ProblÃ¨me de sÃ©curitÃ©">
                <label>Problème de sécurité</label>
                <img src="Problème de sécurité.jpg" alt="ProblÃ¨me_de_sÃ©curitÃ©">
            </div>
            <div class="img-box" data-type="Compte bloquÃ©" style="left: 0px; top: 0px">
                <label>Compte bloqué</label>
                <img src="Compte bloqué.jpg" alt="Compte_bloquÃ©">
            </div>
        </div>

        <!-- FORMULAIRE -->
        <form id="contactForm" method="POST" action="">
            <!-- Champ sujet type cachÃ© -->
            <input type="hidden" id="sujet_type" name="sujet_type" value="">

            <!-- DROPDOWN LANGUE -->
            <label for="language">Choisir la langue</label>
            <select id="language" name="language">
                <option value="">Choisir la langue</option>
                <option value="fr">Francais</option>
                <option value="en">Anglais</option>
                <option value="ar">Arabe</option>
            </select>

            <!-- Champ statut cachÃ© -->
            <input type="hidden" name="statut" value="en attente">

            <!-- BOUTON ENVOYER -->
           <button type="submit" class="btn-submit">
           <i class="fas fa-paper-plane"></i> Suivant
           </button>
            <!-- Inscription -->
            <div class="auto-style1">
                <span class="auto-style3">Vous n'etes pas inscrit ? </span>
                <span class="auto-style4">Inscrivez-vous</span>
            </div>
        </form>
    </section>
</main>

<footer>
    <p>Powered by TEC_MAX 2025 Â© Version 1.0âœ¨</p>
</footer>
<script>
    // Gestion de la sélection du type de problème
    let selectedProblemType = '';

    document.querySelectorAll('.img-box').forEach(box => {
        box.addEventListener('click', function() {
            const problemType = this.getAttribute('data-type');
            
            if (selectedProblemType === problemType) {
                this.classList.remove('selected');
                selectedProblemType = '';
                document.getElementById('sujet_type').value = '';
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            } else {
                document.querySelectorAll('.img-box').forEach(b => {
                    b.classList.remove('selected');
                    b.style.transform = 'scale(1)';
                    b.style.boxShadow = 'none';
                });
                
                this.classList.add('selected');
                selectedProblemType = problemType;
                document.getElementById('sujet_type').value = selectedProblemType;
                this.style.transform = 'scale(1.05)';
                this.style.boxShadow = '0 0 20px var(--yellow)';
            }
            
            removeErrorMessage('image-error');

            document.querySelectorAll('.img-box').forEach(img => {
                img.style.border = 'none';
            });
            
            validateForm();
        });
    });

    function removeErrorMessage(id) {
        const errorElement = document.getElementById(id);
        if (errorElement) {
            errorElement.remove();
        }
    }

    function showErrorMessage(message, elementId, insertAfterElement) {
        removeErrorMessage(elementId);
        
        const errorDiv = document.createElement('div');
        errorDiv.id = elementId;
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'var(--pink)';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '5px';
        errorDiv.style.textAlign = 'center';
        errorDiv.style.animation = 'fadeIn 0.3s ease';
        errorDiv.innerHTML = '⚠️ ' + message;
        
        insertAfterElement.after(errorDiv);
    }

    const form = document.getElementById('contactForm');
    const submitBtn = document.querySelector('.btn-submit');

    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.6';
    submitBtn.style.cursor = 'not-allowed';

    function validateForm() {
        const languageSelect = document.getElementById('language');
        const selectedLanguage = languageSelect.value;
        const imageAndLanguageValid = selectedProblemType && selectedLanguage;

        if (!selectedProblemType) {
            if (!document.getElementById('image-error')) {
                showErrorMessage('Veuillez sélectionner un type de problème', 'image-error', document.querySelector('.image-grid'));
            }
            document.querySelectorAll('.img-box').forEach(img => {
                img.style.border = '2px solid var(--pink)';
                img.style.borderRadius = '12px';
                img.style.padding = '5px';
            });
        } else {
            removeErrorMessage('image-error');
            document.querySelectorAll('.img-box').forEach(img => {
                img.style.border = 'none';
            });
        }

        if (!selectedLanguage) {
            if (!document.getElementById('language-error')) {
                showErrorMessage('Veuillez sélectionner une langue', 'language-error', languageSelect.parentElement);
            }
            languageSelect.classList.add('error-field');
        } else {
            removeErrorMessage('language-error');
            languageSelect.classList.remove('error-field');
        }

        if (imageAndLanguageValid) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            submitBtn.style.background = 'var(--purple)';
            submitBtn.style.boxShadow = '0 0 15px rgba(138,141,255,0.5)';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
            submitBtn.style.background = 'var(--blue-mid)';
            submitBtn.style.boxShadow = 'none';
        }

        return imageAndLanguageValid;
    }

    // 🔥 MODIFICATION PRINCIPALE
    // ➜ SI ON CLIQUE → OUVRIR add.html
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const languageSelect = document.getElementById('language');
        const selectedLanguage = languageSelect.value;
        const imageAndLanguageValid = selectedProblemType && selectedLanguage;

        if (imageAndLanguageValid) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ouverture...';
            this.disabled = true;
            window.location.href = 'add.php';
        } else {
            const errorParams = [];
            if (!selectedProblemType) errorParams.push('photo_non_selectionnee=1');
            if (!selectedLanguage) errorParams.push('langue_non_selectionnee=1');
            errorParams.push('formulaire_incomplet=1');

            const queryString = errorParams.join('&');
            window.location.href = window.location.pathname + '?' + queryString;
        }
    });

    document.getElementById('language').addEventListener('change', function() {
        validateForm();
        
        if (this.value) {
            this.style.borderColor = 'var(--green)';
            this.style.boxShadow = '0 0 10px var(--green)';
            setTimeout(() => {
                this.style.borderColor = 'var(--purple)';
                this.style.boxShadow = '0 0 10px rgba(138,141,255,0.2)';
            }, 1000);
        }
    });

    document.querySelectorAll('.img-box').forEach(box => {
        box.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'scale(1.03)';
                this.style.boxShadow = '0 0 15px var(--purple-light)';
            }
        });
        
        box.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            }
        });
    });

    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('focus', function() {
            this.style.background = 'rgba(255,255,255,0.1)';
            this.classList.remove('error-field');
        });
        
        field.addEventListener('blur', function() {
            this.style.background = 'rgba(255,255,255,0.05)';
        });
    });

    // Ajout styles dynamiques
    const style = document.createElement('style');
    style.textContent = `
        .error-field {
            border-color: var(--pink) !important;
            box-shadow: 0 0 10px var(--pink) !important;
            animation: pulseError 0.5s ease;
        }
        
        @keyframes pulseError {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-message {
            color: var(--pink) !important;
            font-size: 14px;
            text-align: center;
            animation: fadeIn 0.3s ease;
        }

        .img-box.selected::after {
            content: "✓";
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--yellow);
            color: var(--blue-dark);
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        
        .img-box {
            position: relative;
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('formulaire_incomplet')) {
            const errorComments = [];
            
            if (urlParams.has('photo_non_selectionnee')) {
                errorComments.push('📷 Type de problème non sélectionné');
            }
            if (urlParams.has('langue_non_selectionnee')) {
                errorComments.push('🌐 Langue non sélectionnée');
            }
            
            if (errorComments.length > 0) {
                const commentDiv = document.createElement('div');
                commentDiv.className = 'commentaire-error';
                commentDiv.innerHTML = `
                    <strong>❌ Formulaire incomplet :</strong><br>
                    ${errorComments.join('<br>')}<br>
                    <small>Veuillez compléter tous les champs obligatoires</small>
                `;
                
                const header = document.querySelector('header');
                if (header) {
                    header.after(commentDiv);
                }
            }
        }

        validateForm();
    });

    setInterval(validateForm, 500);
</script>
</body>
</html>