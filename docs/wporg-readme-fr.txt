Traduction française de la page WordPress.org (readme.txt), à saisir sur
https://translate.wordpress.org/projects/wp-plugins/skuads-forms-for-elementor/stable-readme/fr/default/
Chaque bloc ci-dessous correspond à une chaîne de la page de traduction.

--- Nom du plugin ---
Skuads Forms for Elementor

--- Description courte ---
Connectez les formulaires Elementor Pro à l'application Skuads et centralisez les envois de formulaires dans l'application.

--- Titres ---
Description
Services externes
À propos de Skuads
Copyright et licence
Journal des modifications

--- Description ---
Skuads Forms for Elementor est l'intégration officielle de Skuads pour les formulaires Elementor Pro. Elle est développée par Skuads et n'est ni affiliée à Elementor ni approuvée par Elementor.

Le plugin détecte les formulaires des pages et modèles Elementor, permet aux administrateurs de choisir les formulaires à connecter, envoie automatiquement les nouveaux envois à l'application Skuads et peut synchroniser manuellement les 30 envois les plus récents.

Les synchronisations manuelles répétées utilisent des identifiants d'envoi stables pour éviter les leads en double.

Un compte Skuads actif, un Site UUID, une clé API et Elementor Pro sont nécessaires.

Pour en savoir plus, rendez-vous sur https://skuads.com/ ou sur le centre d'aide Skuads : https://support.skuads.com/.

--- Services externes ---
Ce plugin se connecte à l'application Skuads (https://app.skuads.com/), un service fourni par Skuads, l'auteur de ce plugin. Ce service est nécessaire pour recevoir les envois des formulaires Elementor du site sous forme de leads dans le compte Skuads du propriétaire du site. Rien n'est envoyé tant qu'un administrateur n'a pas activé le plugin, enregistré un Site UUID et une clé API valides, et sélectionné les formulaires à capter. Les envois des formulaires non sélectionnés ne sont jamais transmis.

Lorsqu'un administrateur enregistre les réglages, le plugin envoie le Site UUID, la clé API (en jeton Bearer), ainsi que l'identifiant et le nom de chaque formulaire sélectionné à https://app.skuads.com/api/connect-leadforms pour vérifier la connexion.

Chaque fois qu'un visiteur envoie l'un des formulaires Elementor Pro sélectionnés, le plugin envoie le Site UUID, l'identifiant et le nom du formulaire, les valeurs saisies (qui peuvent contenir des données personnelles comme le nom, l'adresse e-mail, le numéro de téléphone ou le message) et la date de l'envoi à https://app.skuads.com/webhook/leadforms.

Lorsqu'un administrateur clique sur « Manually sync entries », le plugin envoie les mêmes données pour les 30 envois les plus récents des formulaires sélectionnés à https://app.skuads.com/webhook/leadforms.

Conditions d'utilisation de Skuads : https://support.skuads.com/fr/legal/terms/
Politique de confidentialité de Skuads : https://support.skuads.com/fr/legal/privacy/

--- À propos de Skuads ---
Skuads est une entreprise française de marketing digital qui associe une application marketing tout-en-un à l'accompagnement d'experts. Skuads aide les entreprises à gérer leurs sites web, leur SEO, leur publicité en ligne, leurs réseaux sociaux, leur CRM, leurs leads et leur marketing automation.

Skuads - 1480 Avenue d'Arménie, Bât C, Pôle Yvon Morandat, 13120 Gardanne, France.

Site web : https://skuads.com/
Support : https://support.skuads.com/
E-mail : support@skuads.com
Téléphone : +33 4 82 29 60 75

--- Copyright et licence ---
Copyright (C) 2026 Skuads. Le plugin Skuads Forms for Elementor a été créé par Skuads et appartient à Skuads. Skuads est titulaire des droits d'auteur du plugin et de son code source original.

Ce plugin est un logiciel libre distribué sous la licence publique générale GNU, version 2 ou toute version ultérieure (GPL-2.0-or-later). Consultez le fichier LICENSE.md du dépôt source pour plus de détails.

--- Journal des modifications ---
Envoie les données uniquement lorsque le plugin est activé et configuré, et seulement pour les formulaires sélectionnés (synchronisation automatique et manuelle).
Le plugin reste désactivé tant qu'un administrateur ne l'a pas activé.
Renomme le plugin en Skuads Forms for Elementor.
Charge le script de la page de réglages avec wp_enqueue_script.
Documente le service Skuads utilisé par le plugin.
Déclare la dépendance à Elementor.
Échappe tout l'affichage de l'administration, ajoute un nonce et une vérification des droits à la synchronisation manuelle, nettoie les réglages enregistrés.
Ajoute des identifiants d'envoi stables pour que les synchronisations manuelles répétées ne créent pas de leads en double.
Détecte les formulaires directement dans les pages et modèles Elementor, y compris ceux qui n'ont encore reçu aucun envoi.
