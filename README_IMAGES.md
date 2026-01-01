# Guide des Images Produits

## ✅ Images générées avec succès !

**154 images** ont été générées pour **22 produits**.

### Statistiques
- **1 image principale** par produit (affichée dans le catalogue)
- **6 images supplémentaires** par produit (galerie de détails)
- **Total : 7 images minimum par produit**

### Emplacement des images
Les images sont stockées dans : `uploads/products/`

Format de nommage :
- Image principale : `product_{id}_main.jpg`
- Images supplémentaires : `product_{id}_1.jpg`, `product_{id}_2.jpg`, etc.

### Caractéristiques des images
- **Taille** : 800x800 pixels
- **Format** : JPEG (qualité 90%)
- **Style** : Placeholders colorés avec gradient et texte
- **Couleurs** : Varient selon la catégorie du produit

### Comment utiliser

#### 1. Voir les images générées
- Ouvrez la boutique : `http://localhost/diexo/shop.php`
- Cliquez sur un produit pour voir toutes ses images

#### 2. Regénérer les images
Si vous voulez regénérer les images :
```
http://localhost/diexo/generate_product_images.php
```

#### 3. Remplacer par de vraies photos
Pour remplacer les placeholders par de vraies photos :

**Option A : Via l'interface admin**
1. Allez dans `admin/products.php`
2. Cliquez sur "Modifier" pour un produit
3. Uploadez une nouvelle image principale

**Option B : Via le panneau vendeur**
1. Allez dans `vendeur/products.php`
2. Modifiez vos produits et uploadez des images

**Option C : Manuellement**
1. Remplacez les fichiers dans `uploads/products/`
2. Gardez les mêmes noms de fichiers
3. Ou mettez à jour la base de données avec les nouveaux noms

### Ajouter plus d'images à un produit

Pour ajouter des images supplémentaires à un produit existant :

1. **Via SQL** :
```sql
INSERT INTO product_images (product_id, image_path, ordre) 
VALUES (1, 'nouvelle_image.jpg', 7);
```

2. **Via l'interface** (à développer) :
   - Ajouter un formulaire d'upload multiple dans `admin/product_edit.php`

### Structure de la base de données

**Table `products`** :
- `image_principale` : Nom du fichier de l'image principale

**Table `product_images`** :
- `product_id` : ID du produit
- `image_path` : Nom du fichier
- `ordre` : Ordre d'affichage (1, 2, 3, ...)

### Notes importantes

⚠️ **Les images actuelles sont des placeholders**
- Elles sont générées automatiquement avec des couleurs
- Vous devriez les remplacer par de vraies photos de produits
- Les placeholders servent uniquement pour les tests et le développement

✅ **Les images sont optimisées**
- Format JPEG pour un bon compromis qualité/taille
- Taille fixe 800x800px pour la cohérence
- Qualité 90% pour de bonnes performances

### Problèmes courants

**Les images ne s'affichent pas ?**
1. Vérifiez que le dossier `uploads/products/` existe
2. Vérifiez les permissions (lecture/écriture)
3. Vérifiez l'URL dans `config/config.php` (UPLOAD_URL)

**Voulez-vous supprimer toutes les images ?**
```sql
DELETE FROM product_images;
UPDATE products SET image_principale = NULL;
```
Puis supprimez les fichiers dans `uploads/products/`

### Prochaines étapes

1. ✅ Images générées
2. 📸 Remplacer par de vraies photos
3. 🎨 Personnaliser les couleurs par catégorie
4. 📱 Optimiser pour mobile
5. 🔍 Ajouter un système de zoom sur les images

