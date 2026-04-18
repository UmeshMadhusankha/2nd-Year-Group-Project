-- Add 'completed' to repairerquote.status enum so finalized repairer quotes can be stored explicitly.
ALTER TABLE repairerquote
MODIFY COLUMN status ENUM('pending','accepted','completed','rejected','expired') DEFAULT 'pending';
