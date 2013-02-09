UPDATE item SET vendor_price = 8   WHERE name LIKE 'Lump of Tin';
UPDATE item SET vendor_price = 16  WHERE name LIKE 'Lump of Coal';
UPDATE item SET vendor_price = 48  WHERE name LIKE 'Lump of Primordium';
UPDATE item SET vendor_price = 8   WHERE name LIKE 'Spool of Jute Thread';
UPDATE item SET vendor_price = 16  WHERE name LIKE 'Spool of Wool Thread';
UPDATE item SET vendor_price = 24  WHERE name LIKE 'Spool of Cotton Thread';
UPDATE item SET vendor_price = 32  WHERE name LIKE 'Spool of Linen Thread';
UPDATE item SET vendor_price = 48  WHERE name LIKE 'Spool of Silk Thread';
UPDATE item SET vendor_price = 64  WHERE name LIKE 'Spool of Gossamer Thread';
UPDATE item SET vendor_price = 496 WHERE name LIKE 'Minor Rune of Holding';
UPDATE item SET vendor_price = 1480   WHERE name LIKE 'Rune of Holding';
UPDATE item SET vendor_price = 5000   WHERE name LIKE 'Major Rune of Holding';
UPDATE item SET vendor_price = 20000  WHERE name LIKE 'Greater Rune of Holding';
UPDATE item SET vendor_price = 100000 WHERE name LIKE 'Superior Rune of Holding';
-- incorrect: UPDATE item SET vendor_price = 2  WHERE name LIKE 'Tomato';
-- incorrect: UPDATE item SET vendor_price = 4  WHERE name LIKE 'Ginger Root';
-- incorrect: UPDATE item SET vendor_price = 2  WHERE name LIKE 'Basil Leaf';
-- incorrect: UPDATE item SET vendor_price = 2  WHERE name LIKE 'Bell Pepper';

UPDATE item SET vendor_price = 8  WHERE name LIKE 'Jar of Vinegar';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Packet of Baking Powder';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Jar of Vegetable Oil';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Packet of Salt';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Bag of Sugar';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Jug of Water';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Bag of Starch';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Bag of Flour';
UPDATE item SET vendor_price = 8  WHERE name LIKE 'Bottle of Soy Sauce';


-- automatically set by new script: UPDATE item SET karma_price = 2 WHERE name LIKE 'Cheese Wedge';
-- automatically set by new script: UPDATE item SET karma_price = 2 WHERE name LIKE 'Glass of Buttermilk';
-- automatically set by new script: UPDATE item SET karma_price = 2 WHERE name LIKE 'Packet of Yeast';
-- automatically set by new script: UPDATE item SET karma_price = 2 WHERE name LIKE 'Rice Ball';
-- automatically set by new script: UPDATE item SET karma_price = 3 WHERE name LIKE 'Bowl of Sour Cream';

-- incorrect: UPDATE item SET karma_price = 1000  WHERE name LIKE "Adept's Training Manual";
-- incorrect: UPDATE item SET karma_price = 10000 WHERE name LIKE "Master's Training Manual";
-- incorrect: UPDATE item SET karma_price = 20000 WHERE name LIKE "Grandmaster's Training Manual";
