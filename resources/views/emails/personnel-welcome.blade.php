<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Suivi Académique IUC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .content {
            margin: 20px 0;
        }
        .credentials {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .credentials h3 {
            margin-top: 0;
            color: #007bff;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 5px;
        }
        .credential-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .credential-value {
            color: #007bff;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Bienvenue dans le Système de Suivi Académique IUC</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $personnel->prenom_pers ?? '' }} {{ $personnel->nom_pers }}</strong>,</p>

            <p>Nous sommes ravis de vous accueillir dans notre système de suivi académique. Votre compte a été créé avec succès.</p>

            <p>Vous trouverez ci-dessous vos identifiants de connexion :</p>

            <div class="credentials">
                <h3>🔐 Vos identifiants de connexion</h3>
                <div class="credential-item">
                    <span class="credential-label">Nom d'utilisateur :</span>
                    <span class="credential-value">{{ $personnel->login_pers }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Mot de passe :</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Type de compte :</span>
                    <span class="credential-value">{{ $personnel->type_pers }}</span>
                </div>
            </div>

            <div class="warning">
                <strong>⚠️ Important :</strong> Pour des raisons de sécurité, nous vous recommandons fortement de changer votre mot de passe lors de votre première connexion.
            </div>

            <p>Vous pouvez maintenant vous connecter à l'application en utilisant ces identifiants.</p>

            <p>Si vous avez des questions ou besoin d'assistance, n'hésitez pas à nous contacter.</p>

            <p>Cordialement,<br>
            <strong>L'équipe du Suivi Académique IUC</strong></p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} Suivi Académique IUC - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>


