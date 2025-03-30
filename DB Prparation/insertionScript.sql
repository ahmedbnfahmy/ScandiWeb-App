-- Insert Categories
INSERT INTO categories (name, __typename) VALUES
('all', 'Category'),
('clothes', 'Category'),
('tech', 'Category');

-- Insert Products
INSERT INTO products (id, name, inStock, description, category, brand, __typename) VALUES
('huarache-x-stussy-le', 'Nike Air Huarache Le', true, '<p>Great sneakers for everyday use!</p>', 'clothes', 'Nike x Stussy', 'Product'),
('jacket-canada-goosee', 'Jacket', true, '<p>Awesome winter jacket</p>', 'clothes', 'Canada Goose', 'Product'),
('ps-5', 'PlayStation 5', true, '<p>A good gaming console. Plays games of PS4! Enjoy if you can buy it mwahahahaha</p>', 'tech', 'Sony', 'Product'),
('xbox-series-s', 'Xbox Series S 512GB', false, '<div><ul><li><span>Hardware-beschleunigtes Raytracing macht dein Spiel noch realistischer</span></li><li><span>Spiele Games mit bis zu 120 Bilder pro Sekunde</span></li><li><span>Minimiere Ladezeiten mit einer speziell entwickelten 512GB NVMe SSD und wechsle mit Quick Resume nahtlos zwischen mehreren Spielen.</span></li><li><span>Xbox Smart Delivery stellt sicher, dass du die beste Version deines Spiels spielst, egal, auf welcher Konsole du spielst</span></li><li><span>Spiele deine Xbox One-Spiele auf deiner Xbox Series S weiter. Deine Fortschritte, Erfolge und Freundesliste werden automatisch auf das neue System übertragen.</span></li><li><span>Erwecke deine Spiele und Filme mit innovativem 3D Raumklang zum Leben</span></li><li><span>Der brandneue Xbox Wireless Controller zeichnet sich durch höchste Präzision, eine neue Share-Taste und verbesserte Ergonomie aus</span></li><li><span>Ultra-niedrige Latenz verbessert die Reaktionszeit von Controller zum Fernseher</span></li><li><span>Verwende dein Xbox One-Gaming-Zubehör -einschließlich Controller, Headsets und mehr</span></li><li><span>Erweitere deinen Speicher mit der Seagate 1 TB-Erweiterungskarte für Xbox Series X (separat erhältlich) und streame 4K-Videos von Disney+, Netflix, Amazon, Microsoft Movies &amp; TV und mehr</span></li></ul></div>', 'tech', 'Microsoft', 'Product'),
('apple-imac-2021', 'iMac 2021', true, 'The new iMac!', 'tech', 'Apple', 'Product'),
('apple-iphone-12-pro', 'iPhone 12 Pro', true, 'This is iPhone 12. Nothing else to say.', 'tech', 'Apple', 'Product'),
('apple-airpods-pro', 'AirPods Pro', false, '<h3>Magic like you''ve never heard</h3><p>AirPods Pro have been designed to deliver Active Noise Cancellation for immersive sound, Transparency mode so you can hear your surroundings, and a customizable fit for all-day comfort. Just like AirPods, AirPods Pro connect magically to your iPhone or Apple Watch. And they''re ready to use right out of the case.</p><h3>Active Noise Cancellation</h3><p>Incredibly light noise-cancelling headphones, AirPods Pro block out your environment so you can focus on what you''re listening to. AirPods Pro use two microphones, an outward-facing microphone and an inward-facing microphone, to create superior noise cancellation. By continuously adapting to the geometry of your ear and the fit of the ear tips, Active Noise Cancellation silences the world to keep you fully tuned in to your music, podcasts, and calls.</p><h3>Transparency mode</h3><p>Switch to Transparency mode and AirPods Pro let the outside sound in, allowing you to hear and connect to your surroundings. Outward- and inward-facing microphones enable AirPods Pro to undo the sound-isolating effect of the silicone tips so things sound and feel natural, like when you''re talking to people around you.</p><h3>All-new design</h3><p>AirPods Pro offer a more customizable fit with three sizes of flexible silicone tips to choose from. With an internal taper, they conform to the shape of your ear, securing your AirPods Pro in place and creating an exceptional seal for superior noise cancellation.</p><h3>Amazing audio quality</h3><p>A custom-built high-excursion, low-distortion driver delivers powerful bass. A superefficient high dynamic range amplifier produces pure, incredibly clear sound while also extending battery life. And Adaptive EQ automatically tunes music to suit the shape of your ear for a rich, consistent listening experience.</p><h3>Even more magical</h3><p>The Apple-designed H1 chip delivers incredibly low audio latency. A force sensor on the stem makes it easy to control music and calls and switch between Active Noise Cancellation and Transparency mode. Announce Messages with Siri gives you the option to have Siri read your messages through your AirPods. And with Audio Sharing, you and a friend can share the same audio stream on two sets of AirPods — so you can play a game, watch a movie, or listen to a song together.</p>', 'tech', 'Apple', 'Product'),
('apple-airtag', 'AirTag', true, '<h1>Lose your knack for losing things.</h1><p>AirTag is an easy way to keep track of your stuff. Attach one to your keys, slip another one in your backpack. And just like that, they''re on your radar in the Find My app. AirTag has your back.</p>', 'tech', 'Apple', 'Product');

-- Insert Product Gallery
INSERT INTO product_gallery (product_id, image_url) VALUES
-- Nike Air Huarache Le
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087'),
-- Jacket
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058169/product-image/2409L_61_o.png'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_1333,c_scale,f_auto,q_auto:best/v1634058159/product-image/2409L_61_p.png'),
-- PlayStation 5
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg'),
-- Xbox Series S
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg'),
-- iMac 2021
('apple-imac-2021', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000'),
-- iPhone 12 Pro
('apple-iphone-12-pro', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&amp;hei=1112&amp;fmt=jpeg&amp;qlt=80&amp;.v=1604021663000'),
-- AirPods Pro
('apple-airpods-pro', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000'),
-- AirTag
('apple-airtag', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airtag-double-select-202104?wid=445&hei=370&fmt=jpeg&qlt=95&.v=1617761672000');

-- Insert Attributes and Attribute Items
-- Nike Air Huarache Le - Size
INSERT INTO attributes (product_id, name, type) VALUES ('huarache-x-stussy-le', 'Size', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, '40', '40', '40', 'Attribute'),
(@last_attr_id, '41', '41', '41', 'Attribute'),
(@last_attr_id, '42', '42', '42', 'Attribute'),
(@last_attr_id, '43', '43', '43', 'Attribute');

-- Jacket - Size
INSERT INTO attributes (product_id, name, type) VALUES ('jacket-canada-goosee', 'Size', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, 'Small', 'S', 'Small', 'Attribute'),
(@last_attr_id, 'Medium', 'M', 'Medium', 'Attribute'),
(@last_attr_id, 'Large', 'L', 'Large', 'Attribute'),
(@last_attr_id, 'Extra Large', 'XL', 'Extra Large', 'Attribute');

-- PlayStation 5 - Color
INSERT INTO attributes (product_id, name, type) VALUES ('ps-5', 'Color', 'swatch');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, 'Green', '#44FF03', 'Green', 'Attribute'),
(@last_attr_id, 'Cyan', '#03FFF7', 'Cyan', 'Attribute'),
(@last_attr_id, 'Blue', '#030BFF', 'Blue', 'Attribute'),
(@last_attr_id, 'Black', '#000000', 'Black', 'Attribute'),
(@last_attr_id, 'White', '#FFFFFF', 'White', 'Attribute');

-- PlayStation 5 - Capacity
INSERT INTO attributes (product_id, name, type) VALUES ('ps-5', 'Capacity', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, '512G', '512G', '512G', 'Attribute'),
(@last_attr_id, '1T', '1T', '1T', 'Attribute');

-- Xbox Series S - Color
INSERT INTO attributes (product_id, name, type) VALUES ('xbox-series-s', 'Color', 'swatch');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, 'Green', '#44FF03', 'Green', 'Attribute'),
(@last_attr_id, 'Cyan', '#03FFF7', 'Cyan', 'Attribute'),
(@last_attr_id, 'Blue', '#030BFF', 'Blue', 'Attribute'),
(@last_attr_id, 'Black', '#000000', 'Black', 'Attribute'),
(@last_attr_id, 'White', '#FFFFFF', 'White', 'Attribute');

-- Xbox Series S - Capacity
INSERT INTO attributes (product_id, name, type) VALUES ('xbox-series-s', 'Capacity', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, '512G', '512G', '512G', 'Attribute'),
(@last_attr_id, '1T', '1T', '1T', 'Attribute');

-- iMac 2021 - Capacity
INSERT INTO attributes (product_id, name, type) VALUES ('apple-imac-2021', 'Capacity', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, '256GB', '256GB', '256GB', 'Attribute'),
(@last_attr_id, '512GB', '512GB', '512GB', 'Attribute');

-- iMac 2021 - USB 3 ports
INSERT INTO attributes (product_id, name, type) VALUES ('apple-imac-2021', 'With USB 3 ports', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, 'Yes', 'Yes', 'Yes', 'Attribute'),
(@last_attr_id, 'No', 'No', 'No', 'Attribute');

-- iMac 2021 - Touch ID
INSERT INTO attributes (product_id, name, type) VALUES ('apple-imac-2021', 'Touch ID in keyboard', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, 'Yes', 'Yes', 'Yes', 'Attribute'),
(@last_attr_id, 'No', 'No', 'No', 'Attribute');

-- iPhone 12 Pro - Capacity
INSERT INTO attributes (product_id, name, type) VALUES ('apple-iphone-12-pro', 'Capacity', 'text');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, '512G', '512G', '512G', 'Attribute'),
(@last_attr_id, '1T', '1T', '1T', 'Attribute');

-- iPhone 12 Pro - Color
INSERT INTO attributes (product_id, name, type) VALUES ('apple-iphone-12-pro', 'Color', 'swatch');
SET @last_attr_id = LAST_INSERT_ID();
INSERT INTO attribute_items (attribute_id, display_value, value, item_id, __typename) VALUES
(@last_attr_id, 'Green', '#44FF03', 'Green', 'Attribute'),
(@last_attr_id, 'Cyan', '#03FFF7', 'Cyan', 'Attribute'),
(@last_attr_id, 'Blue', '#030BFF', 'Blue', 'Attribute'),
(@last_attr_id, 'Black', '#000000', 'Black', 'Attribute'),
(@last_attr_id, 'White', '#FFFFFF', 'White', 'Attribute');

-- Insert Prices
INSERT INTO prices (product_id, amount, currency_label, currency_symbol, __typename) VALUES
('huarache-x-stussy-le', 144.69, 'USD', '$', 'Price'),
('jacket-canada-goosee', 518.47, 'USD', '$', 'Price'),
('ps-5', 844.02, 'USD', '$', 'Price'),
('xbox-series-s', 333.99, 'USD', '$', 'Price'),
('apple-imac-2021', 1688.03, 'USD', '$', 'Price'),
('apple-iphone-12-pro', 1000.76, 'USD', '$', 'Price'),
('apple-airpods-pro', 300.23, 'USD', '$', 'Price'),
('apple-airtag', 120.57, 'USD', '$', 'Price');