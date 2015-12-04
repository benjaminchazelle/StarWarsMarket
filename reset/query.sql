-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- G?r?e : Sam 31 Octobre 2015 ?3:23
-- Version du serveur: 5.1.31
-- Version de PHP: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de donn?: `iut_swm2`
--

-- --------------------------------------------------------

--
-- Structure de la table `bids`
--

DROP TABLE IF EXISTS bids;
CREATE TABLE IF NOT EXISTS `bids` (
  `bid_id` int(11) NOT NULL AUTO_INCREMENT,
  `bid_object_id` int(11) NOT NULL,
  `bid_bidder_user_id` int(11) NOT NULL,
  `bid_price` float NOT NULL,
  `bid_date` int(11) NOT NULL,
  PRIMARY KEY (`bid_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=91 ;

--
-- Contenu de la table `bids`
--

INSERT INTO `bids` (`bid_id`, `bid_object_id`, `bid_bidder_user_id`, `bid_price`, `bid_date`) VALUES
(25, 51, 4, 290, 1446315242),
(26, 57, 4, 320, 1446315248),
(27, 63, 4, 90, 1446315254),
(28, 60, 4, 140, 1446315259),
(29, 65, 4, 100, 1446315263),
(30, 62, 4, 330, 1446315267),
(31, 61, 4, 210, 1446315286),
(32, 69, 1, 610, 1446315394),
(33, 68, 1, 305, 1446315397),
(34, 67, 1, 525, 1446315406),
(35, 66, 1, 135, 1446315414),
(36, 56, 1, 105, 1446315422),
(37, 59, 1, 485, 1446315430),
(38, 63, 1, 95, 1446324715),
(39, 60, 1, 145, 1446324717),
(40, 64, 1, 105, 1446324723),
(41, 61, 1, 215, 1446324725),
(42, 51, 2, 295, 1446324770),
(43, 53, 2, 645, 1446324772),
(44, 56, 2, 110, 1446324811),
(45, 55, 2, 450, 1446324816),
(46, 54, 2, 80, 1446324821),
(47, 57, 2, 325, 1446324823),
(48, 60, 2, 150, 1446324832),
(49, 68, 2, 310, 1446324837),
(50, 69, 2, 650, 1446324842),
(51, 52, 3, 145, 1446324880),
(52, 57, 3, 330, 1446324884),
(53, 55, 3, 455, 1446324887),
(54, 65, 3, 105, 1446324894),
(55, 63, 3, 100, 1446324897),
(56, 68, 3, 315, 1446324899),
(57, 67, 3, 535, 1446324903),
(58, 51, 5, 300, 1446325041),
(59, 60, 5, 160, 1446325055),
(60, 64, 5, 115, 1446325059),
(61, 63, 5, 110, 1446325067),
(62, 59, 5, 490, 1446325072),
(63, 69, 5, 660, 1446325079),
(64, 67, 5, 540, 1446325083),
(65, 56, 1, 115, 1446325195),
(66, 65, 1, 110, 1446325200),
(67, 59, 1, 500, 1446325204),
(68, 63, 1, 120, 1446325211),
(69, 68, 1, 320, 1446325215),
(70, 69, 1, 665, 1446325308),
(71, 60, 4, 165, 1446325427),
(72, 65, 4, 115, 1446325434),
(73, 61, 4, 220, 1446325439),
(74, 51, 3, 305, 1446326240),
(75, 56, 3, 120, 1446326244),
(76, 53, 3, 650, 1446326249),
(77, 67, 3, 550, 1446326260),
(78, 65, 3, 120, 1446326274),
(79, 51, 4, 320, 1446326344),
(80, 56, 4, 150, 1446326347),
(81, 53, 4, 660, 1446326354),
(82, 55, 4, 460, 1446326358),
(83, 51, 5, 350, 1446326391),
(84, 53, 5, 670, 1446326402),
(85, 59, 5, 520, 1446326406),
(86, 60, 5, 180, 1446326409),
(87, 61, 5, 250, 1446326412),
(88, 68, 5, 330, 1446326417),
(89, 69, 5, 670, 1446326427),
(90, 60, 1, 185, 1446327219);

-- --------------------------------------------------------

--
-- Structure de la table `objects`
--

DROP TABLE IF EXISTS objects;
CREATE TABLE IF NOT EXISTS `objects` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_name` varchar(64) NOT NULL,
  `object_description` varchar(512) NOT NULL,
  `object_picture_url` varchar(256) NOT NULL,
  `object_minimal_price` float NOT NULL,
  `object_seller_user_id` int(11) NOT NULL,
  `object_start_date` int(11) NOT NULL,
  `object_end_date` int(11) NOT NULL,
  `object_statut` int(11) NOT NULL,
  PRIMARY KEY (`object_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=70 ;

--
-- Contenu de la table `objects`
--

INSERT INTO `objects` (`object_id`, `object_name`, `object_description`, `object_picture_url`, `object_minimal_price`, `object_seller_user_id`, `object_start_date`, `object_end_date`, `object_statut`) VALUES
(51, 'Chewbacca', 'Chewbacca est un personnage de Star Wars. Légendaire guerrier Wookie et co-pilote du Faucon Millenium aux cotés de Han Solo, Chewbacca fait partie du noyau de rebelles qui ont restauré la liberté dans la galaxie. Connu pour se mettre très facilement en colère et sa précision à l''arbalète, Chewbacca a aussi un grand cœur et fait preuve d''une loyauté indéfectible envers ses amis. ', 'static/1446311028-chewie-db_2c0efea2.jpeg', 270, 1, 1446246000, 1446591600, 1),
(52, '2-1B', '\r\n2-1B est un droïde médical, programmé pour diagnostiquer et traiter les blessures et les maladies qui peuvent toucher des millions d''espèces de la galaxie. 2-1B possède des bras modulaires qui lui permettent d''utiliser une gamme d''instruments chirurgicaux et autres instruments médicaux basés sur les besoins de ses patients.\r\n', 'static/1446311426-2-1b-droid-main-image_546a90ad.jpeg', 140, 1, 1446246000, 1446678000, 1),
(53, 'AT-AT', '\r\nLe All Terrain Armored Transport, ou AT-AT walker, est un transport à quatre pattes et le véhicule de combat utilisé par les forces Impériale. D''une hauteur de plus de 20 mètres avec une armure blast-imperméable contre les électrodéposition, ce véhicule massif est également utilisé aussi bien pour à des fins psychologique car il offre un avantage tactique.\r\n', 'static/1446311728-AT-AT_89d0105f.jpeg', 640, 1, 1446246000, 1446678000, 1),
(54, 'C-3PO', 'C-3PO (dans la version originale) ou Z-6PO1 (dans la version française des épisodes IV, V et VI) est un « droïde de protocole » de la saga Star Wars.\r\n\r\nC''est un droïde protocolaire de forme humanoïde, particulièrement loquace et, selon ses dires, « maîtrisant plus de six millions de formes de communication »', 'static/1446311854-C-3PO-See-Threepio_68fe125c.jpeg', 75, 1, 1446246000, 1446591600, 1),
(55, 'Bantha', 'Le bantha est l''une des plus adaptables créatures herbivores de la galaxie, qui peut être trouvé sur plusieurs mondes dont Tatooine. Ils sont capables de vivre dans presque toutes les conditions environnementales extrêmes et peuvent survivre sans eau ni nourriture pendant plusieurs semaines. Ils sont quadrupèdes, recouverts d''une épaisse fourrure et ont de grandes cornes incurvées.', 'static/1446312016-bantha-main-image_b3ab933d.jpeg', 425, 5, 1446246000, 1447110000, 1),
(56, 'Dark Maul', 'Dark Maul est un mâle Zabrak originaire de la planète Dathomir. Comme ses frères Feral et Savage Opress, il vient du clan des Frères de la Nuit , qui est lié au clan des Sœurs de la Nuit .  ', 'static/1446312132-Darth-Maul_632eb5af.jpeg', 100, 5, 1446246000, 1446937200, 1),
(57, 'Chopper', 'Appartenant à Hera Syndulla tout comme le Ghost, il fut témoin de la création de la petite équipe rebelle avec tout d''abord l''arrivé de Kanan Jarrus. Ensemble ils réalisèrent de nombreuses missions finissant le plus souvent en course poursuite contre les chasseurs TIE de l''Empire Galactique comme dans le court métrage La Machine dans le Ghost. Le rôle de Chopper est alors d''aider Hera au pilotage du vaisseau. ', 'static/1446312218-chopper-databank_916fc2ff.jpeg', 310, 5, 1446246000, 1446332400, 1),
(58, 'AT-DP', 'Les Nacelles de Défense Tout Terrain (abrégé en ND-TT ou AT-DP en anglais pour All Terrain Defense Pod) sont des bipodes construit par Kuat Drive Yards pour l''Empire Galactique. De nombreux sont visible sur Lothal pour contrer le groupe de rebelle du Ghost. ', 'static/1446312305-AT-DP_11f721ba.jpeg', 150, 5, 1446246000, 1446678000, 1),
(59, 'Module de course', 'Petite nacelle ouverte (pod) tractée par de puissants moteurs indépendants grâce à des câbles de commande flexibles, le pilote atteint des vitesses supérieures à 800 km à l''heure.', 'static/1446312602-databank_anakinskywalkerspodracer_01_169_fe359d32.jpeg', 480, 3, 1446246000, 1447196400, 1),
(60, 'Speeder Flash', 'Les speeder Flash sont des landspeeders utilisés par les gardes royaux de Naboo.\r\n\r\nIl sert aux patrouilles urbaines et à la chasse aux malfaiteurs. Son altitude maximum de deux mètres est suffisante dans les rues pavées de Theed et les herbages plats de Naboo. Peu différent de la version civile, l''engin des Forces de la Sécurité Royale est néanmoins un véritable appareil militaire, solide et fiable.', 'static/1446312686-databank_flashspeeder_01_169_48978def.jpeg', 120, 3, 1446246000, 1447369200, 1),
(61, 'Figrin D''an', 'Figrin D''an est un musicien Bith originaire de Clak''dor VII, leader du groupe des Modal Nodes. Il joue de la corne Kloo.\r\n\r\nC''est un parieur qui rembourse ses dettes avec sa musique, tout en évitant d''apporter des ennuis aux autres membres du groupe. Son surnom est "Furieux" du à son envie de dominer les autres. ', 'static/1446312795-figrin-dan_59ce7b09.jpeg', 200, 3, 1446246000, 1446332400, 1),
(62, 'B-Wing', 'Créé par le Commandant Ackbar et construit par les chantiers navals Verpine, le B-Wing fut d''abord construit en un seul exemplaire qui fut testé lors d''une mission sur Yunko IX. Au vu du succès de la mission, le Rébellion décida d''en commander un escadron complet. Le B-Wing est ensuite rapidement devenu l''un des chasseurs stellaires les plus polyvalents. ', 'static/1446312921-databank_bwingfighter_01_169_460cc528.jpeg', 320, 3, 1446246000, 1446678000, 1),
(63, 'AT-TE', 'Le Renfort Tactique Tout Terrain (abrégé en RT-TT ou AT-TE en anglais pour All Terrain Tactical Enforcer) est un hexapode de combat utilisé par la République Galactique et l''Empire Galactique. ', 'static/1446313157-databank_attewalker_01_169_4292c02c.jpeg', 85, 2, 1446246000, 1447801200, 1),
(64, 'BB-8', 'BB-8 est un droïde astromech. Il a la particularité de se déplacer sur une sorte de ballon. Il appartient au pilote de la Résistance Poe Dameron pour qui il est le copilote dans un X-Wing T-70. Ses aventures le mèneront vers Jakku où il rencontrera Rey. ', 'static/1446313322-ep7_ia_162323_j_077412a0.jpeg', 100, 2, 1446246000, 1447196400, 1),
(65, 'Comlink', 'Le comlink est un système de communication portatif utilisé sur de nombreuses planètes de la galaxie.\r\n\r\nL''Empire Galactique, par exemple, équipa tous ces officiers de comlink. Avant cela, la Grande Armée de la République avait équipé un comlink sur chaque armure de soldat clone. L''équipage du Ghost dispose aussi de comlink. ', 'static/1446313592-image_882c5d68.jpeg', 95, 2, 1446246000, 1446505200, 1),
(66, 'U9-C4', 'U9-C4 est un droïde astromech appartenant au Jedi Thongla Jur durant la Guerre des Clones. Il rejoignit la D-Squad commandée par Meebur Gascon pour réaliser une mission de haute importance dans un vaisseau Séparatiste, mission qui fut un succès après de nombreuses péripéties. ', 'static/1446313720-image_416c6dc3.jpeg', 130, 2, 1446246000, 1447110000, 1),
(67, 'Étoile de la Mort', 'Cette nouvelle arme de l’Empire Galactique a été construite suivant la destruction de la première Étoile de la Mort par Luke Skywalker et l''Alliance Rebelle dans la Bataille de Yavin. ', 'static/1446313832-Death-Star-II_b5760154.jpeg', 500, 4, 1446246000, 1447196400, 1),
(68, 'Chasseur TIE', 'Le chasseur TIE est le premier succès de la longue série des chasseurs TIE (Twin Ion Engine). Ce chasseur est rapide et maniable, mais il souffre de sa faible résistance et de son autonomie nulle, redoutable en combat rapproché, le chasseur TIE équipe la très grande majoritée de la chasse impériale à raison de plus d''un million d''unitées en service en l''an 3 ap.BY, son bruit caractéristique et son aspect menaçant en font un engin très reconnaissable. ', 'static/1446313988-vaders-tie-fighter_8bcb92e1.jpeg', 300, 4, 1446246000, 1447369200, 1),
(69, 'Destroyer Impérial', 'Les destroyers Stellaire de classe Impérial sont des Star Destroyer développé par Kuat Drive Yards pour l''Empire Galactique, il remplace les destroyers Stellaire de classe Venator de la République Galactique. ', 'static/1446314057-Star-Destroyer_ab6b94bb.jpeg', 600, 4, 1446246000, 1447455600, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_firstname` varchar(32) NOT NULL,
  `user_lastname` varchar(32) NOT NULL,
  `user_email` varchar(128) NOT NULL,
  `user_password` varchar(128) NOT NULL,
  `user_address` varchar(256) NOT NULL,
  `user_phone` varchar(16) NOT NULL,
  `user_rank` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=7 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`user_id`, `user_firstname`, `user_lastname`, `user_email`, `user_password`, `user_address`, `user_phone`, `user_rank`) VALUES
(1, 'Georges', 'Lucas', 'georges.lucas@yopmail.com', '5cf433063bf0fb932132a75ad94b3093ff2ee8ec', '42 IHM Street', '0010203040', 1),
(2, 'Yoda', 'Master', 'yoda.master@yopmail.com', '5cf433063bf0fb932132a75ad94b3093ff2ee8ec', '47 Dagobah Planet', '0015253545', 0),
(3, 'Mark', 'Hamill', 'mark.hamill@yopmail.com', '5cf433063bf0fb932132a75ad94b3093ff2ee8ec', '456-7 Rogue Two Avenue', '0027476787', 0),
(4, 'Dark', 'Vador', 'dark.vador@yopmail.com', '5cf433063bf0fb932132a75ad94b3093ff2ee8ec', '66 Death Star', '0038597193', 0),
(5, 'R2', 'D2', 'r2d2@yopmail.com', '5cf433063bf0fb932132a75ad94b3093ff2ee8ec', '652 Tu tii tutu ti', '0017217200', 0);
