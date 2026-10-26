-- ============================================================
-- GlowCare – Authentic Real Brand Sample Demonstration Data
-- Brands: CeraVe, The Ordinary, La Roche-Posay, COSRX, Paula's Choice,
--         Bioderma, Clinique, Neutrogena, Beauty of Joseon, Innisfree, etc.
-- ============================================================

USE glowcare;

-- ============================================================
-- SAMPLE USERS
-- Passwords: "Password123!" for all demo users
-- Hash generated with PHP password_hash('Password123!', PASSWORD_BCRYPT)
-- ============================================================
INSERT INTO users (full_name, email, password_hash, phone, date_of_birth, gender, role) VALUES
('Admin User',    'admin@glowcare.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', '1995-03-15', 'Female', 'admin'),
('Priya Sharma',  'priya@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543211', '1998-07-22', 'Female', 'user'),
('Rahul Mehta',   'rahul@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543212', '1997-11-10', 'Male',   'user'),
('Ananya Gupta',  'ananya@example.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543213', '2000-01-05', 'Female', 'user'),
('Demo Student',  'demo@glowcare.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543214', '2001-08-15', 'Other',  'user');

-- ============================================================
-- SKIN ASSESSMENTS
-- ============================================================
INSERT INTO skin_assessments (user_id, skin_type, oily_feeling, dry_tight, easily_irritated, concerns) VALUES
(2, 'combination', 'sometimes', 'sometimes', 'no',        'acne,dark_spots'),
(3, 'oily',        'yes',       'no',        'no',        'acne,large_pores'),
(4, 'dry',         'no',        'yes',       'sometimes', 'dryness,fine_lines'),
(5, 'combination', 'sometimes', 'sometimes', 'no',        'acne,dark_spots,dullness');

-- ============================================================
-- CATEGORIES
-- ============================================================
INSERT INTO categories (category_name, description) VALUES
('Cleanser',     'Gentle foaming, gel, and hydrating cream washes'),
('Moisturizer',  'Lipid-replenishing barrier creams and water gels'),
('Sunscreen',    'Broad spectrum UV filters & invisible mineral shields'),
('Serum',        'Concentrated active treatments (Niacinamide, Hyaluronic, Cica)'),
('Toner',        'Balancing essences and gentle chemical exfoliants'),
('Eye & Lip Care','Targeted delicate orbital and lipid lip treatments'),
('Exfoliator',   'Salicylic, Glycolic and Lactic pore clarifying solutions');

-- ============================================================
-- REAL BRAND PRODUCTS
-- ============================================================
INSERT INTO products (name, brand, category_id, price, description, ingredients, usage_instructions, precautions, skin_type, image_path) VALUES
('Hydrating Facial Cleanser', 'CeraVe', 1, 1250.00, 'Dermatologist-developed gentle non-foaming wash formulated with 3 essential ceramides and hyaluronic acid to cleanse and hydrate.', 'Aqua/Water, Glycerin, Cetearyl Alcohol, Ceramide NP, Ceramide AP, Ceramide EOP, Hyaluronic Acid, Phytosphingosine, Cholesterol.', 'Wet skin with lukewarm water. Massage cleanser into skin in a circular motion. Rinse thoroughly.', 'For external use only. Avoid direct eye contact.', 'dry', 'cerave_hydrating_cleanser.jpg'),

('Low pH Good Morning Gel Cleanser', 'COSRX', 1, 850.00, 'Formulated with purifying botanical ingredients and mild Betaine Salicylate to gently strip away sebum while preserving skin pH.', 'Water, Cocamidopropyl Betaine, Sodium Lauroyl Methyl Isethionate, Betaine Salicylate 0.5%, Tea Tree Leaf Oil, Allantoin.', 'Gently massage a small amount on wet skin morning or night. Rinse with tepid water.', 'Do not use on broken or wounded skin.', 'oily', 'cosrx_low_ph_cleanser.jpg'),

('Effaclar Purifying Foaming Gel', 'La Roche-Posay', 1, 1490.00, 'French pharmacy foaming wash featuring soothing Thermal Spring Water and Zinc PCA to visibly reduce shine without over-drying.', 'Aqua/Water, Sodium Laureth Sulfate, PEG-8, Coco-Betaine, Zinc PCA, Citric Acid, Thermal Spring Water.', 'Lather in hands with warm water. Apply to face, gently massaging. Rinse thoroughly.', 'Avoid delicate eye contour area.', 'oily', 'lrp_effaclar_gel.jpg'),

('Sensibio H2O Micellar Water', 'Bioderma', 1, 995.00, 'Cult-classic micellar makeup remover and daily cleanser created specifically for sensitive and intolerant skin.', 'Aqua/Water, PEG-6 Caprylic/Capric Glycerides, Cucumis Sativus (Cucumber) Fruit Extract, Mannitol, Xylitol, Rhamnose.', 'Soak a cotton pad and sweep across face and eyes. No rinsing required.', 'Dermatologically tested on sensitive skin.', 'sensitive', 'bioderma_sensibio.jpg'),

('Moisturizing Cream with Ceramides', 'CeraVe', 2, 1450.00, 'Rich, non-greasy barrier repair moisturizer featuring 3 bio-identical ceramides and hyaluronic acid with MVE delivery.', 'Aqua, Glycerin, Cetearyl Alcohol, Caprylic Triglyceride, Petrolatum, Ceramide NP, Ceramide AP, Ceramide EOP, Hyaluronic Acid.', 'Apply generously to face and neck as the sealing step of your routine.', 'Suitable for dry to very dry skin.', 'dry', 'cerave_moisturizing_cream.jpg'),

('Hydro Boost Water Gel', 'Neutrogena', 2, 950.00, 'Award-winning oil-free water gel that instantly quenches dehydrated skin and locks in moisture with purified Hyaluronic Acid.', 'Water, Dimethicone, Glycerin, Phenoxyethanol, Sodium Hyaluronate, Olive Leaf Extract.', 'Apply evenly across freshly washed face every morning and evening.', '100% oil-free and non-comedogenic.', 'oily', 'neutrogena_hydro_boost.jpg'),

('Moisture Surge 100H Hydrator', 'Clinique', 2, 2100.00, 'Lush gel-cream powered by exclusive Aloe Bioferment and dual-weight Hyaluronic Acid for 100 hours of continuous moisture.', 'Water/Aqua, Dimethicone, Butylene Glycol, Glycerin, Trehalose, Sucrose, Lactobacillus Ferment Lysate, Aloe Water.', 'Use morning and night on clean skin. Can also be used as a 5-minute moisture mask.', 'Dermatologist developed. 100% fragrance free.', 'combination', 'clinique_moisture_surge.jpg'),

('Relief Sun: Rice + Probiotics SPF 50+', 'Beauty of Joseon', 3, 1299.00, 'Viral Korean organic sunscreen with 30% Rice Extract and grain fermented probiotics leaving zero white cast and a natural dewy finish.', 'Water, Oryza Sativa (Rice) Extract 30%, Propanediol, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Niacinamide 2%.', 'At the last step of morning skincare, evenly spread two-finger length over face and neck.', 'Reapply every 2-3 hours during direct sun exposure.', 'all', 'boj_relief_sun.jpg'),

('Anthelios UVMune 400 Fluid SPF 50+', 'La Roche-Posay', 3, 1890.00, 'Features the revolutionary Mexoryl 400 filter protecting against ultra-long UVA rays with invisible non-greasy finish.', 'Aqua/Water, Alcohol Denat., Triethyl Citrate, Silica, Ethylhexyl Salicylate, Mexoryl 400, Thermal Spring Water.', 'Shake vigorously before use. Apply 20 minutes before sun exposure.', 'Very water and sweat resistant.', 'combination', 'lrp_anthelios_fluid.jpg'),

('Niacinamide 10% + Zinc 1%', 'The Ordinary', 4, 600.00, 'High-strength active serum that visibly targets blemishes, controls excess sebum, and minimizes enlarged pore appearance.', 'Aqua (Water), Niacinamide 10%, Pentylene Glycol, Zinc PCA 1%, Tamarindus Indica Seed Gum, Phenoxyethanol.', 'Apply a few drops to entire face morning and evening before heavier creams.', 'Do not use simultaneously with pure Vitamin C in the same step.', 'oily', 'ordinary_niacinamide.jpg'),

('Hyaluronic Acid 2% + B5', 'The Ordinary', 4, 750.00, 'Multi-depth hydration formulation combining low, medium and high molecular weight Hyaluronic Acid with Provitamin B5.', 'Aqua (Water), Sodium Hyaluronate 2%, Panthenol (Vitamin B5), Ahnfeltia Concinna Extract, Glycerin.', 'Apply a few drops to clean damp face morning and night before oils and creams.', 'Always apply to slightly damp skin.', 'dry', 'ordinary_hyaluronic.jpg'),

('Advanced Snail 96 Mucin Power Essence', 'COSRX', 4, 1450.00, 'Lightweight soothing essence formulated with 96.3% Snail Secretion Filtrate to protect moisture loss and soothe irritation.', 'Snail Secretion Filtrate 96.3%, Betaine, Butylene Glycol, 1,2-Hexanediol, Sodium Hyaluronate, Allantoin, Panthenol.', 'After cleansing and toning, apply a small amount to entire face and gently pat.', 'Cruelty-free formulation.', 'all', 'cosrx_snail_mucin.jpg'),

('Skin Perfecting 2% BHA Liquid Exfoliant', 'Paula\'s Choice', 7, 1200.00, 'Holy-grail leave-on exfoliant containing 2% Salicylic Acid that unclogs enlarged pores, smooths wrinkles, and evens out tone.', 'Water (Aqua), Methylpropanediol, Butylene Glycol, Salicylic Acid (2%), Green Tea Leaf Extract, Sodium Hydroxide.', 'Apply once or twice daily after cleansing. Lightly soak a cotton pad and sweep across face.', 'Always wear broad spectrum sunscreen daily when using BHA.', 'combination', 'paulas_choice_bha.jpg'),

('Heartleaf 77% Soothing Toner', 'Anua', 5, 1650.00, 'Korea\'s #1 bestselling soothing toner packed with 77% Houttuynia Cordata extract to calm irritation and cool acne.', 'Houttuynia Cordata Extract 77%, Aqua, Glycerin, Betaine, Centella Asiatica Extract, Panthenol, Chamomile Extract.', 'After cleansing, dispense onto hands or cotton pad and gently pat across entire face.', 'Hypoallergenic tested on sensitive skin.', 'sensitive', 'anua_heartleaf_toner.jpg'),

('Rice Toner with 77.78% Yeoju Rice', 'I\'m From', 5, 1750.00, 'Two-phase milky toner formulated with 77.78% organic Yeoju Rice extract providing luminous glass-skin glow.', 'Oryza Sativa (Rice) Extract 77.78%, Methylpropanediol, Triethylhexanoin, Niacinamide, Portulaca Oleracea Extract.', 'Shake well before use to blend the emulsion and moisture layers. Tap into skin using palms.', 'Shake before each use to emulsify.', 'dry', 'im_from_rice_toner.jpg'),

('Lip Sleeping Mask (Berry)', 'Laneige', 6, 600.00, 'Leave-on lip balm mask enriched with Berry Fruit Complex, Murumuru Seed Butter, and Vitamin C for plump lips.', 'Diisostearyl Malate, Murumuru Seed Butter, Shea Butter, Vitamin C, Berry Fruit Complex.', 'Apply generously to lips in evening and leave on overnight.', 'For external lip care only.', 'all', 'laneige_lip_mask.jpg'),

('Creamy Eye Treatment with Avocado', 'Kiehl\'s', 6, 2600.00, 'Nourishing avocado under-eye cream with rich fatty acids and Shea Butter delivering immediate de-puffing hydration.', 'Aqua/Water, Shea Butter, Avocado Oil, Butylene Glycol, Beta-Carotene.', 'Warm a rice-grain amount between ring fingertips until creamy, then tap along eye bone.', 'Ophthalmologist tested for all skin types.', 'dry', 'kiehls_avocado_eye.jpg'),

('Green Tea Seed Hyaluronic Serum', 'Innisfree', 4, 1650.00, 'Hydrating serum infused with Jeju Green Tea Tri-biotics and 5 types of hyaluronic acid that strengthens barriers.', 'Water, Propanediol, Glycerin, Jeju Green Tea Seed Oil, Sodium Hyaluronate, Panthenol, Ceramide NP.', 'Apply 2-3 pumps onto face immediately after cleansing.', 'Store away from direct heat and sunlight.', 'all', 'innisfree_green_tea.jpg');

-- ============================================================
-- ROUTINES
-- ============================================================
INSERT INTO routines (user_id, routine_name, routine_type) VALUES
(5, 'Demo Morning Barrier Glow',   'morning'),
(5, 'Demo Evening Barrier Repair', 'evening'),
(2, 'Priya AM Glow',               'morning'),
(2, 'Priya PM Recovery',           'evening');

-- ============================================================
-- ROUTINE STEPS
-- ============================================================
INSERT INTO routine_steps (routine_id, product_id, step_name, step_order) VALUES
(1, 1,  'CeraVe Hydrating Facial Cleanser',           1),
(1, 14, 'Anua Heartleaf 77% Soothing Toner',          2),
(1, 10, 'The Ordinary Niacinamide 10% + Zinc 1%',     3),
(1, 6,  'Neutrogena Hydro Boost Water Gel',           4),
(1, 8,  'Beauty of Joseon Relief Sun SPF 50+',        5),

(2, 4,  'Bioderma Sensibio H2O Micellar Pre-Cleanse', 1),
(2, 2,  'COSRX Low pH Good Morning Gel Cleanser',     2),
(2, 12, 'COSRX Advanced Snail 96 Mucin Essence',      3),
(2, 17, 'Kiehl\'s Creamy Avocado Eye Treatment',      4),
(2, 5,  'CeraVe Moisturizing Cream with Ceramides',   5);

-- ============================================================
-- SAMPLE ORDERS (RDBMS Lab Demonstration)
-- ============================================================
INSERT INTO orders (order_id, user_id, total_amount, discount_amount, shipping_fee, payment_method, payment_status, order_status, shipping_address) VALUES
('GC-2026-849201', 5, 2300.00, 460.00, 0.00, 'upi', 'paid', 'Confirmed - Preparing Dispatch', 'Flat 402, Radiant Palms, Bengaluru, Karnataka - 560001'),
('GC-2026-512934', 2, 1850.00, 0.00,   0.00, 'card','paid', 'Delivered',                       '12 Rosewood Enclave, Indiranagar, Bengaluru - 560038');

INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES
('GC-2026-849201', 8,  1, 1299.00),
('GC-2026-849201', 10, 1, 600.00),
('GC-2026-849201', 16, 1, 600.00),
('GC-2026-512934', 1,  1, 1250.00),
('GC-2026-512934', 10, 1, 600.00);
