<?php

namespace App\Utils;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\CartOrder;
use App\Entity\CartProduct;
use App\Entity\SalesStatus;
use App\Entity\OrderProduct;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InitialDatas
{
    private $cart = [
        [
            'Price' => '20.00',
            'Name' => 'Grand panier',
            'Slug' => 'grand-panier',
            'OnSale' => '1',
            'Archived' => '0',
            'ArchivedAt' => NULL
        ],
        [
            'Price' => '15.00',
            'Name' => 'Panier de saison',
            'Slug' => 'panier-de-saison',
            'OnSale' => '1',
            'Archived' => '0',
            'ArchivedAt' => NULL
        ],
        [
            'Price' => '10.00',
            'Name' => 'Petit panier',
            'Slug' => 'petit-panier',
            'OnSale' => '1',
            'Archived' => '0',
            'ArchivedAt' => NULL
        ]
    ];

    private $category = array(
        array('Name' => 'Fruits', 'Slug' => 'fruits', 'Image' => NULL, 'Description' => NULL),
        array('Name' => 'Légumes', 'Slug' => 'legumes', 'Image' => NULL, 'Description' => NULL),
        array('Name' => 'Epicerie', 'Slug' => 'epicerie', 'Image' => NULL, 'Description' => NULL)
    );

    private $product = array(
        array('Category' => '2', 'Product' => NULL, 'Name' => 'P.d.T nouvelles Twinner', 'Slug' => 'p-d-t-nouvelles-twinner-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '6310a8757a1a2_1000015_10151771168527188_222891671_n.jpg', 'Price' => '2.20', 'Colorimetry' => 'hot', 'Description' => 'Idéales au four, pour les frites ou les purées', 'UpdatedAt' => '2022-09-01 14:41:25', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Blettes blanches', 'Slug' => 'blettes-blanches-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310a9975a049_11110211_871582636213575_8514927050142822656_n.jpg', 'Price' => '3.00', 'Colorimetry' => 'cold', 'Description' => 'A manger comme des épinards ou avec une sauce tomate !', 'UpdatedAt' => '2022-09-01 14:46:15', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Courgettes', 'Slug' => 'courgettes-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '63188f8bd7fce_courgettes-multi.jpg', 'Price' => '2.80', 'Colorimetry' => 'cold', 'Description' => 'Seule, en ratatouille ou dans des desserts, de nombreuses recettes en perspectives pour le bonheur des petits et des grands !', 'UpdatedAt' => '2022-09-07 14:33:15', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'tomates rondes rouges', 'Slug' => 'tomates-rondes-rouges-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310ad03dc69d_tomates-rouges.jpg', 'Price' => '3.50', 'Colorimetry' => 'hot', 'Description' => 'En salade ou farcie, accompagnée d\'une bonne mozarella di buffala, laissez vous tentez par ce fruit goûteux riche en vitamines !', 'UpdatedAt' => '2022-09-01 15:00:51', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Tomates anciennes', 'Slug' => 'tomates-anciennes-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310ad6ded015_67186394_2388736751164815_5748941509562990592_n.webp', 'Price' => '5.00', 'Colorimetry' => 'hot', 'Description' => 'Ananas, Noire de Crimée, Cœur de Bœuf, un panaché de couleurs pour les plus gourmands !', 'UpdatedAt' => '2022-09-01 15:02:37', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Salade', 'Slug' => 'salade-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310adf1c42a1_115690675_3217000718338410_2253561556297092170_n.webp', 'Price' => '1.50', 'Colorimetry' => 'cold', 'Description' => 'De la rouge, de la verte, parfois frisées, parfois croquantes, osez nos différentes variétés pour un menu en toute légèreté !', 'UpdatedAt' => '2022-09-01 15:04:49', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Carottes oranges', 'Slug' => 'carottes-oranges-1-lot-s-2', 'Stock' => '999', 'Unity' => 'lot(s)', 'Image' => '6310ae711b6eb_carottes-botte.webp', 'Price' => '3.20', 'Colorimetry' => 'hot', 'Description' => 'Croquantes ou fondantes, à vous de choisir vos recettes pour un repas en toute amabilité !', 'UpdatedAt' => '2022-09-01 15:06:57', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '1', 'Product' => NULL, 'Name' => 'Poire Williams', 'Slug' => 'poire-williams-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '63189ad11c0f9_1610550168.webp', 'Price' => '4.20', 'Colorimetry' => 'hot', 'Description' => 'Juteuse, très parfumée et sucrée.', 'UpdatedAt' => '2022-09-07 15:21:21', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '1', 'Product' => NULL, 'Name' => 'Poire Conférence', 'Slug' => 'poire-conference-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '63189b2c40912_fiche_poire_conference.jpg', 'Price' => '3.90', 'Colorimetry' => 'cold', 'Description' => 'Chair fine, fondante, bien sucrée, juteuse et parfumée.', 'UpdatedAt' => '2022-09-07 15:22:52', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme Betterave', 'Slug' => 'jus-de-pomme-betterave-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310bc862b60e_jus-pommes.jpg', 'Price' => '3.50', 'Colorimetry' => 'hot', 'Description' => '1L. A boire bien frais. 10 € les 3 bouteilles
    La plupart de ces produits sont fabriqués par nos soins à partir de nos légumes, plantes et fruits, ainsi que d’ingrédients issus de l’agriculture biologique (sucre de canne, vinaigre de cidre, huile d’olive, etc.).', 'UpdatedAt' => '2022-09-01 16:07:02', 'Archived' => '1', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme Fenouil', 'Slug' => 'jus-de-pomme-fenouil-1-btlle-2', 'Stock' => '999', 'Unity' => 'btlle', 'Image' => '63189ab816971_minijus.jpg', 'Price' => '3.50', 'Colorimetry' => 'hot', 'Description' => '1L - Plein de vitamine et rafraîchissant ! 
    La plupart de ces produits sont fabriqués par nos soins à partir de nos ingrédients issus de l’agriculture biologique.', 'UpdatedAt' => '2022-09-07 15:20:56', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme au Thym', 'Slug' => 'jus-de-pomme-au-thym-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => NULL, 'Price' => '3.50', 'Colorimetry' => 'hot', 'Description' => '1L
    La plupart de ces produits sont fabriqués par nos soins à partir de nos légumes, plantes et fruits, ainsi que d’ingrédients issus de l’agriculture biologique.', 'UpdatedAt' => '2022-09-07 15:15:35', 'Archived' => '1', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme Reine des prés', 'Slug' => 'jus-de-pomme-reine-des-pres-1-btlle-2', 'Stock' => '999', 'Unity' => 'btlle', 'Image' => '6310bdc23e906_jus-pommes-coings.jpg', 'Price' => '3.50', 'Colorimetry' => 'hot', 'Description' => '1L', 'UpdatedAt' => '2022-09-01 16:12:18', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme poire Sureau', 'Slug' => 'jus-de-pomme-poire-sureau-1-btlle-2', 'Stock' => '999', 'Unity' => 'btlle', 'Image' => '6310be2233af9_jus-pommes-poires.jpg', 'Price' => '3.90', 'Colorimetry' => 'hot', 'Description' => '1L
    11 € les 3 (identiques ou panachées)', 'UpdatedAt' => '2022-09-01 16:13:54', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme-mûre', 'Slug' => 'jus-de-pomme-mure-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310be5c9cb62_jus-pommes-mures.jpg', 'Price' => '4.95', 'Colorimetry' => 'hot', 'Description' => 'Notre jus pomme-mûre résulte du mélange de 80% de pommes et 20% de mûres, il vous surprendra par sa couleur et vous rafraîchira grâce à sa saveur légèrement acidulée.', 'UpdatedAt' => '2022-09-01 16:14:52', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '1', 'Product' => NULL, 'Name' => 'Cerise Kordia', 'Slug' => 'cerise-kordia-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '6315df2830dca_camila-aramayo-lFo7eFwr8Fs-unsplash.jpg', 'Price' => '3.80', 'Colorimetry' => 'hot', 'Description' => 'Sucrée, à déguster de suite ou en dessert !', 'UpdatedAt' => '2022-09-05 13:36:08', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Jus de pomme prunes', 'Slug' => 'jus-de-pomme-prunes-1-btlle-2', 'Stock' => '999', 'Unity' => 'btlle', 'Image' => '6310bebb0071f_jus-pommes-prunes.jpg', 'Price' => '4.95', 'Colorimetry' => 'hot', 'Description' => 'Notre jus pomme-prune est destinée à nous permettre de savourer de délicieuses prunes toute l\'année.
    Un jus pomme-prune mélangeant 85% de pommes et 15% de prunes.', 'UpdatedAt' => '2022-09-01 16:16:27', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Aubergines', 'Slug' => 'aubergines-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310bf11cad31_aubergine.webp', 'Price' => '3.80', 'Colorimetry' => 'cold', 'Description' => 'Emblématique de la cuisine méditerranéenne. très bénéfique pour l’organisme. En plus d’être très peu calorique, elle est riche en fibres et en antioxydants !', 'UpdatedAt' => '2022-09-01 16:17:53', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '1', 'Product' => NULL, 'Name' => 'Prune Victoria', 'Slug' => 'prune-victoria-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '63189c3665b57_0001195.jpg', 'Price' => '4.00', 'Colorimetry' => 'cold', 'Description' => 'Prune violette à la chair verte, très fruitée.', 'UpdatedAt' => '2022-09-07 15:27:18', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Oignons rouges', 'Slug' => 'oignons-rouges-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310c01434677_oignon-rouge.webp', 'Price' => '3.00', 'Colorimetry' => 'hot', 'Description' => 'Légèrement sucrée et moins piquant, l\'ingrédient indispensable en cuisine !', 'UpdatedAt' => '2022-09-01 16:22:12', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '1', 'Product' => NULL, 'Name' => 'Pomme Gala', 'Slug' => 'pomme-gala-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '63189beb2ef3f_pomme-gala-vrac.jpeg', 'Price' => '3.90', 'Colorimetry' => 'hot', 'Description' => 'La classique, petite, facile à emporter et bien sucrée.', 'UpdatedAt' => '2022-09-07 15:26:03', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '1', 'Product' => NULL, 'Name' => 'Pomme Boskoop', 'Slug' => 'pomme-boskoop-1-kg-2', 'Stock' => '1000', 'Unity' => 'Kg', 'Image' => '63189bb239d34_Pomme-Bio-Boskoop-Alsacienne.jpg', 'Price' => '3.90', 'Colorimetry' => 'hot', 'Description' => 'Pomme à cuire par excellence, acidulée et parfumée.', 'UpdatedAt' => '2022-09-07 15:25:06', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Haricots verts/beurre', 'Slug' => 'haricots-verts-beurre-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310c6718c8d7_1732fe71926e6af676c1.jpg', 'Price' => '9.00', 'Colorimetry' => 'cold', 'Description' => 'Des haricots verts. Ou avec du beurre. Au choix. C\'est toi qui choiz\' !', 'UpdatedAt' => '2022-09-01 16:49:21', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Basilic grec (pot)', 'Slug' => 'basilic-grec-pot-1-pot-2', 'Stock' => '999', 'Unity' => 'pot', 'Image' => '6310c715c714f_19291415252_72afea55af_z.webp', 'Price' => '1.50', 'Colorimetry' => 'cold', 'Description' => 'Plante aromatique tout indiquée pour l’assaisonnement ou en pesto !', 'UpdatedAt' => '2022-09-01 16:52:05', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'P.d.T nouvelles Vitabella', 'Slug' => 'p-d-t-nouvelles-vitabella-1-kg-2', 'Stock' => '999', 'Unity' => 'Kg', 'Image' => '6310c7af1ea9d_1000015_10151771168527188_222891671_n.jpg', 'Price' => '2.20', 'Colorimetry' => 'hot', 'Description' => 'Cette pomme de terre reste ferme à la cuisson, idéale pour salades, cuisson à l\'eau ou sautée.', 'UpdatedAt' => '2022-09-01 16:54:39', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Ketchup', 'Slug' => 'ketchup-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310dccde001a_ketchup.jpg', 'Price' => '2.50', 'Colorimetry' => 'hot', 'Description' => 'Parfait pour accompagner les barbecues !', 'UpdatedAt' => '2022-09-01 18:24:45', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Sel Ail des ours', 'Slug' => 'sel-ail-des-ours-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310dd6f4907a_selaildesours.jpg', 'Price' => '3.50', 'Colorimetry' => 'hot', 'Description' => '75 g', 'UpdatedAt' => '2022-09-01 18:27:27', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Vinaigre de cidre artisanal', 'Slug' => 'vinaigre-de-cidre-artisanal-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310e13878f81_vinaigre.jpg', 'Price' => '2.50', 'Colorimetry' => 'cold', 'Description' => 'Fait à la ferme avec nos pommes.', 'UpdatedAt' => '2022-09-01 18:43:36', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Sirop de Basilic citron', 'Slug' => 'sirop-de-basilic-citron-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310e1b0ee272_48367369_2046408812064279_117612138893148160_n.jpg', 'Price' => '4.50', 'Colorimetry' => 'hot', 'Description' => 'Un goût rafraîchissant de thé glacé aux arômes naturels de plante.', 'UpdatedAt' => '2022-09-01 18:45:36', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Pesto Ail des ours', 'Slug' => 'pesto-ail-des-ours-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310e31a9eedc_pesto.jpg', 'Price' => '4.50', 'Colorimetry' => 'cold', 'Description' => '4,50 € le pot de 90g | 7 € le pot de 170g.', 'UpdatedAt' => '2022-09-01 18:51:38', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Vinaigre de cidre fleur de sureau', 'Slug' => 'vinaigre-de-cidre-fleur-de-sureau-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '63189a4e5fb22_vinaigre.jpg', 'Price' => '4.20', 'Colorimetry' => 'hot', 'Description' => '(25 cl) : 4,20 €
    Sa subtile saveur accompagnera salades, légumes croquants et déglacera les viandes à merveille.', 'UpdatedAt' => '2022-09-07 15:19:10', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Tisanes', 'Slug' => 'tisanes-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310e3d299d9e_tisaness.jpg', 'Price' => '5.00', 'Colorimetry' => 'cold', 'Description' => '5 € le sachet de 30g tonique', 'UpdatedAt' => '2022-09-01 18:54:42', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Confiture Poire-Baies de sureau', 'Slug' => 'confiture-poire-baies-de-sureau-1-pot-2', 'Stock' => '999', 'Unity' => 'pot', 'Image' => '6310e422caf27_étale.jpg', 'Price' => '4.20', 'Colorimetry' => 'hot', 'Description' => 'Parfait pour un petit déjeuner savoureux et gourmand !', 'UpdatedAt' => '2022-09-01 18:56:02', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Confit de fleurs de pissenlit', 'Slug' => 'confit-de-fleurs-de-pissenlit-1-pot-2', 'Stock' => '999', 'Unity' => 'pot', 'Image' => '6310e4463c497_étale.jpg', 'Price' => '4.50', 'Colorimetry' => 'cold', 'Description' => 'Le pissenlit est une fleur comestible diurétique et bonne pour la santé !', 'UpdatedAt' => '2022-09-01 18:56:38', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Ketchup du jardin', 'Slug' => 'ketchup-du-jardin-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '631899e96e078_ketchup.jpg', 'Price' => '5.50', 'Colorimetry' => 'hot', 'Description' => '280 g: 5,50 €
    Parfait pour accompagner vos barbecues !', 'UpdatedAt' => '2022-09-07 15:17:29', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Ketchup Black Cherry', 'Slug' => 'ketchup-black-cherry-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '631899f618150_ketchup.jpg', 'Price' => '5.50', 'Colorimetry' => 'hot', 'Description' => 'Parfait pour les barbecues!', 'UpdatedAt' => '2022-09-07 15:17:42', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Soupe l\'épicée de l\'automne', 'Slug' => 'soupe-l-epicee-de-l-automne-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310ea9a7e222_promosoupe.jpg', 'Price' => '2.50', 'Colorimetry' => 'hot', 'Description' => 'Velouté de courge butternut au gingembre (75 cl) - Lot de 3 soupes : 7 €', 'UpdatedAt' => '2022-09-01 19:23:38', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Soupe la Popotte des îles', 'Slug' => 'soupe-la-popotte-des-iles-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '6310ead55e1d5_promosoupe.jpg', 'Price' => '2.50', 'Colorimetry' => 'hot', 'Description' => 'Potimarron au lait de coco (75 cl) - Lot de 3 soupes : 7 €', 'UpdatedAt' => '2022-09-01 19:24:37', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Bortsh', 'Slug' => 'bortsh-1-btlle-2', 'Stock' => '999', 'Unity' => 'btlle', 'Image' => '6310eb0373bce_promosoupe.jpg', 'Price' => '2.50', 'Colorimetry' => 'hot', 'Description' => 'Petits légumes en bouillon façon slave.', 'UpdatedAt' => '2022-09-01 19:25:23', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '3', 'Product' => NULL, 'Name' => 'Mini-bouteilles 25cl', 'Slug' => 'mini-bouteilles-25cl-1-piece-2', 'Stock' => '999', 'Unity' => 'pièce', 'Image' => '63189ac8b9551_minijus.jpg', 'Price' => '2.00', 'Colorimetry' => 'hot', 'Description' => 'Mini-bouteilles de 25 cl (100% pur jus Pomme-Fenouil, Pomme-Betterave, Pomme-Céleri, Pomme au thym ou Pomme au Basilic)', 'UpdatedAt' => '2022-09-07 15:21:12', 'Archived' => '0', 'QuantityUnity' => '1', 'OnSale' => '1'),
        array('Category' => '2', 'Product' => NULL, 'Name' => 'Piment très piquant', 'Slug' => 'piment-tres-piquant-1-g-2', 'Stock' => '999', 'Unity' => 'g', 'Image' => NULL, 'Price' => '3.00', 'Colorimetry' => 'hot', 'Description' => NULL, 'UpdatedAt' => NULL, 'Archived' => '1', 'QuantityUnity' => '1', 'OnSale' => '1')
    );

    private $cartProduct = array(
        array('Cart' => '1', 'Product' => '18', 'Quantity' => '1.000'),
        array('Cart' => '1', 'Product' => '21', 'Quantity' => '1.000'),
        array('Cart' => '1', 'Product' => '16', 'Quantity' => '1.000'),
        array('Cart' => '1', 'Product' => '33', 'Quantity' => '1.000'),
        array('Cart' => '1', 'Product' => '23', 'Quantity' => '1.000'),
        array('Cart' => '2', 'Product' => '18', 'Quantity' => '1.000'),
        array('Cart' => '2', 'Product' => '19', 'Quantity' => '0.500'),
        array('Cart' => '2', 'Product' => '20', 'Quantity' => '0.750'),
        array('Cart' => '2', 'Product' => '21', 'Quantity' => '1.000'),
        array('Cart' => '2', 'Product' => '22', 'Quantity' => '1.000'),
        array('Cart' => '2', 'Product' => '33', 'Quantity' => '0.750'),
        array('Cart' => '2', 'Product' => '35', 'Quantity' => '0.500'),
        array('Cart' => '3', 'Product' => '24', 'Quantity' => '1.000'),
        array('Cart' => '3', 'Product' => '16', 'Quantity' => '1.000'),
        array('Cart' => '3', 'Product' => '19', 'Quantity' => '0.500')
    );

    private $depot = array(
        array('Phone' => '03 03 03 03 03', 'Address' => '1, rue de la tournichette, 59144 Wargnies-le-Petit', 'Name' => 'La Tournichette', 'Available' => '1', 'Informations' => NULL),
        array('Phone' => '03 03 03 03 04', 'Address' => '259d Avenue Henri Barbusse, 59770 Marly', 'Name' => 'Local Endurance', 'Available' => '1', 'Informations' => NULL),
        array('Phone' => '03 03 03 03 05', 'Address' => '29 rue de la Briqueterie, 59600 Maubeuge', 'Name' => 'local Astitouh', 'Available' => '1', 'Informations' => NULL),
        array('Phone' => '03 03 03 03 06', 'Address' => '30 rue de la Gare, 59144 Wargnies-le-Grand', 'Name' => 'Aux Etables de l\'Aunelle', 'Available' => '1', 'Informations' => NULL),
        array('Phone' => '03 03 03 03 07', 'Address' => '8 rue Juhel, 59530 Le Quesnoy', 'Name' => 'Centre Lowendal', 'Available' => '1', 'Informations' => NULL),
        array('Phone' => '03 03 03 03 08', 'Address' => '47 rue de la Gare, 59570 Bavay', 'Name' => 'Local Angibaud', 'Available' => '0', 'Informations' => NULL),
        array('Phone' => '03 03 03 03 09', 'Address' => '18 bis rue de Maubeuge, 59570 Bavay', 'Name' => 'Local aux Serres', 'Available' => '0', 'Informations' => NULL)
    );

    private $user = array(
        array(
            'Email' => 'superadmin@admin.com',
            'Password' => 'admin',
            'Firstname' => 'Junior',
            'Lastname' => 'Super-Admin',
            'Phone' => '0606060606',
            'Address' => NULL,
            'Roles' => ["ROLE_SUPER_ADMIN"],
            'StripeCustomerId' => NULL,
            'ApiToken' => NULL,
            'ApiTokenUpdatedAt' => '2022-10-17 18:05:10',
            'Sub' => NULL,
            'EmailNotifications' => '1',
            'TempApiToken' => NULL,
            'TempToken' => NULL,
            'TempTokenUpdatedAt' => NULL,
            'EmailChecked' => '1',
            'EmailToken' => NULL,
            'EmailTokenUpdatedAt' => NULL),
        array('Email' => 'admin@admin.com', 'Password' => 'admin', 'Firstname' => 'Pierre', 'Lastname' => 'Admin', 'Phone' => '0755777777', 'Address' => NULL, 'Roles' => ["ROLE_ADMIN"], 'StripeCustomerId' => NULL, 'ApiToken' => NULL, 'ApiTokenUpdatedAt' => '2022-10-11 19:05:55', 'Sub' => NULL, 'EmailNotifications' => '1', 'TempApiToken' => NULL, 'TempToken' => NULL, 'TempTokenUpdatedAt' => NULL, 'EmailChecked' => '1', 'EmailToken' => NULL, 'EmailTokenUpdatedAt' => NULL),
        array('Email' => 'user@user.com', 'Password' => 'user', 'Firstname' => 'Jean', 'Lastname' => 'User', 'Phone' => '0607080910', 'Address' => NULL, 'Roles' => ["ROLE_USER"], 'StripeCustomerId' => NULL, 'ApiToken' => NULL, 'ApiTokenUpdatedAt' => '2022-10-17 18:48:00', 'Sub' => NULL, 'EmailNotifications' => '1', 'TempApiToken' => NULL, 'TempToken' => NULL, 'TempTokenUpdatedAt' => NULL, 'EmailChecked' => '1', 'EmailToken' => NULL, 'EmailTokenUpdatedAt' => NULL),
    );

    // private $order = array(); // TODO

    // private $cartOrder = []; // TODO

    // private $orderProduct = array(); // TODO

    private $salesStatus = array(
        array('Enable' => '0', 'StartAt' => '2022-11-13 18:07:54', 'EndAt' => '2022-11-13 21:45:38', 'Name' => 'status', 'StartMail' => NULL, 'EndMail' => NULL, 'SendMail' => '0', 'StartMailSubject' => NULL, 'EndMailSubject' => NULL)
    );

    private $em;
    private $categoryRepository;
    private $productRepository;
    private $cartRepository;
    private $userPasswordHasher;
    private $faker;
    private $fakerFr;

    public function __construct(
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        UserPasswordHasherInterface $userPasswordHasher
        )
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->faker = \Faker\Factory::create();
        $this->fakerFr = \Faker\Factory::create('fr_FR');
    }

    public function createCarts()
    {
        foreach ($this->cart as $currentCart) {
            $cart = new Cart;
            foreach ($currentCart as $property => $value) {
                $method = 'set' . $property;
                $cart->$method($value);
            }
            $this->em->persist($cart);
        }

        $this->em->flush();

        return $this;
    }

    public function createCategories()
    {
        foreach ($this->category as $currentCategory) {
            $category = new Category;
            foreach ($currentCategory as $property => $value) {
                $method = 'set' . $property;
                $category->$method($value);
            }
            $this->em->persist($category);
        }

        $this->em->flush();

        return $this;
    }

    public function createProducts()
    {
        foreach ($this->product as $currentProduct) {
            $product = new Product;
            foreach ($currentProduct as $property => $value) {
                $method = 'set' . $property;

                if ($method === 'setCategory') {
                    $product->$method($this->categoryRepository->find($value));
                } elseif ($method === 'setProduct') {
                    $product->$method($value === NULL ? $value : $this->productRepository->find($value));
                } elseif ($method === 'setUpdatedAt') {
                    $product->$method($value === NULL ? $value : new DateTimeImmutable($value));
                } else {
                    $product->$method($value);
                }
            }
            $this->em->persist($product);
        }

        $this->em->flush();

        return $this;
    }

    public function createCartProducts()
    {
        foreach ($this->cartProduct as $currentCartProduct) {
            $cartProduct = new CartProduct;
            foreach ($currentCartProduct as $property => $value) {
                $method = 'set' . $property;

                if ($method === 'setCart') {
                    $cartProduct->$method($this->cartRepository->find($value));
                } elseif ($method === 'setProduct') {
                    $cartProduct->$method($this->productRepository->find($value));
                } else {
                    $cartProduct->$method($value);
                }
            }
            $this->em->persist($cartProduct);
        }

        $this->em->flush();

        return $this;
    }

    public function createDepots()
    {
        foreach ($this->depot as $currentDepot) {
            $depot = new Depot;
            foreach ($currentDepot as $property => $value) {
                $method = 'set' . $property;
                $depot->$method($value);
            }
            $this->em->persist($depot);
        }

        $this->em->flush();

        return $this;
    }

    public function createUsers(int $nbUser = 0)
    {
        foreach ($this->user as $currentUser) {
            $user = new User;
            foreach ($currentUser as $property => $value) {
                $method = 'set' . $property;
                if ($method === 'setApiTokenUpdatedAt' || $method === 'setTempTokenUpdatedAt' || $method === 'setEmailTokenUpdatedAt') {
                    $user->$method($value === NULL ? $value : new DateTimeImmutable($value));
                } elseif ($method === 'setPassword') {
                    $user->$method($this->userPasswordHasher->hashPassword($user, $value));
                } else {
                    $user->$method($value);
                }
            }
            $this->em->persist($user);
        }

        for($i = 0; $i < $nbUser; $i++)
        {
            $userObj = new User();

            $userObj->setFirstname($this->fakerFr->firstName());
            $userObj->setLastname($this->fakerFr->lastName());
            $userObj->setEmail($this->fakerFr->unique()->email());

            $userObj->setRoles(['ROLE_USER']);
            $userObj->setPassword($this->userPasswordHasher->hashPassword($userObj, $userObj->getFirstname() . $userObj->getLastname()));

            $phoneUser = $this->fakerFr->unique()->serviceNumber();
            $phoneNoSpaceUser = str_replace(' ', '', $phoneUser);
            $userObj->setPhone($phoneNoSpaceUser);

            $userObj->setAddress($this->faker->address());
            $userObj->setEmailNotifications(false);
            $userObj->setEmailChecked(false);

            $this->em->persist($userObj);
        }
        
        $this->em->flush();

        return $this;
    }

    public function createSalesStatus()
    {
        foreach ($this->salesStatus as $currentSalesStatus) {
            $salesStatus = new SalesStatus;
            foreach ($currentSalesStatus as $property => $value) {
                $method = 'set' . $property;

                if ($method === 'setStartAt' || $method === 'setEndAt') {
                    $salesStatus->$method($value === NULL ? $value : new DateTimeImmutable($value));
                } else {
                    $salesStatus->$method($value);
                }
            }
            $this->em->persist($salesStatus);
        }

        $this->em->flush();

        return $this;
    }

    public function createOrders(int $nbOrders)
    {
        // $nbOrders = 50;
    
        $faker = $this->faker;
        $manager = $this->em;

        $randomUsers = $faker->randomElements($manager->getRepository(User::class)->findAll(), 50, true);
        $allDepots = $manager->getRepository(Depot::class)->findAll();

        for($i = 0 ; $i<$nbOrders; $i++)
        {
            $order = new Order();
            $orderType = $faker->randomElement(['cart', 'products', 'mix']);

            if ($orderType === 'cart') {

               $this->chooseCart($order);
                
                
            } elseif ($orderType === 'products') {
                
                $this->chooseProducts($order);
                
            } elseif ($orderType === 'mix') {

                $this->chooseCart($order);
                $this->chooseProducts($order);
            }

            $dateOredered = $faker->dateTimeBetween('-1 week');
            $dateOrederedImmutable = $dateOredered instanceof \DateTimeImmutable ? $dateOredered : \DateTimeImmutable::createFromMutable($dateOredered);
            $order->setDateOrder($dateOrederedImmutable);

            $paymentRandomStatus = $faker->randomElement(['yes', 'no']);
            if($paymentRandomStatus === 'yes') {
                $order->setPaidAt($dateOrederedImmutable);
            }
            $order->setPaymentStatus($paymentRandomStatus);

            $order->setDepot($faker->randomElement($allDepots));

            $order->setUser($randomUsers[$i]);

            $deliverRandomStatus = $faker->randomElement(['yes', 'no']);
            $deliveredDate = new \DateTimeImmutable();
            if($deliverRandomStatus === 'yes') {
                $order->setDeliveredAt($deliveredDate);

                if($paymentRandomStatus === 'no') {
                    $order->setPaidAt($deliveredDate);
                    $order->setPaymentStatus('yes');
                }
            }
            $order->setDeliverStatus($deliverRandomStatus);

            $manager->persist($order);

            // $manager->persist($order);
        }


        $manager->flush();
    }

    /**
     * Add a random cart (big or small) to the order
     */
    private function chooseCart($order)
    {
        $manager = $this->em;
        
        $priceOrder = $order->getPrice() == null ? 0 : $order->getPrice();

        $cartOrder = new CartOrder();
        $cartOrder->setQuantity(1);
        $cartOrder->setOrders($order);
        

        $cart = $manager->getRepository(Cart::class)->findOneBy(['slug' => $this->faker->randomElement(['grand-panier', 'petit-panier', 'panier-de-saison'])]);
        $cartOrder->setCart($cart);

        $order->setPrice($priceOrder + $cart->getPrice());

        $manager->persist($cartOrder);

        // $manager->flush();

        return $order;
    }

    /**
    * Add random products to the order
    */
    private function chooseProducts($order)
    {
        $manager = $this->em;
        $faker = $this->faker;

        $priceOrder = $order->getPrice() == null ? 0 : $order->getPrice();

        $randomProducts = $faker->randomElements(
                                $manager->getRepository(Product::class)->findAll(),
                                $faker->numberBetween(1, 5)
                            );

        foreach($randomProducts as $currentProduct) {
            $orderProduct = new OrderProduct;
            $orderProduct->setProduct($currentProduct);
            $orderProduct->setOrders($order);
            $orderProduct->setQuantity(1);
            $priceOrder += $currentProduct->getPrice();

            $manager->persist($orderProduct);
        }

        $order->setPrice($priceOrder);

        // $manager->flush();

        return $order;
    }
}