-- Seed Categories for Repairer Registration
-- Run this if your category table is empty

USE fix_lanka;

-- Insert service categories
INSERT INTO `category` (`category_id`, `name`) VALUES
(1, 'Plumbing'),
(2, 'Electrical'),
(3, 'HVAC'),
(4, 'Cleaning'),
(5, 'Carpentry'),
(6, 'Painting'),
(7, 'Appliance Repair'),
(8, 'Roofing'),
(9, 'Landscaping'),
(10, 'Pest Control'),
(11, 'Home Security'),
(12, 'Interior Design'),
(13, 'Flooring'),
(14, 'Masonry'),
(15, 'Welding'),
(16, 'Glass & Mirror'),
(17, 'Tile Work'),
(18, 'Drywall'),
(19, 'Insulation'),
(20, 'Window Installation')
ON DUPLICATE KEY UPDATE name = VALUES(name);
