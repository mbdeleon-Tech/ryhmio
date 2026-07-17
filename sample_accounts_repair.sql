-- Migrates older sample emails and repairs both confirmed Rhymio accounts.
-- The password for both accounts is: password

UPDATE users SET email = 'admin@rhymio.test'
WHERE email = 'admin@tunestack.test';

UPDATE users SET
    complete_name = 'Rhymio Admin',
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    complete_address = 'Admin Office, Manila',
    contact_number = '09170000000',
    role = 'admin',
    is_confirmed = 1,
    confirm_token = NULL
WHERE email = 'admin@rhymio.test';

INSERT INTO users
    (complete_name, email, password, complete_address, contact_number, role, is_confirmed, confirm_token)
SELECT
    'Rhymio Admin', 'admin@rhymio.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin Office, Manila', '09170000000', 'admin', 1, NULL
WHERE NOT EXISTS (
    SELECT id FROM users WHERE email = 'admin@rhymio.test'
);

UPDATE users SET email = 'buyer@rhymio.test'
WHERE email = 'buyer@tunestack.test';

UPDATE users SET
    complete_name = 'Rhymio Buyer',
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    complete_address = 'Buyer Street, Quezon City',
    contact_number = '09280000000',
    role = 'buyer',
    is_confirmed = 1,
    confirm_token = NULL
WHERE email = 'buyer@rhymio.test';

INSERT INTO users
    (complete_name, email, password, complete_address, contact_number, role, is_confirmed, confirm_token)
SELECT
    'Rhymio Buyer', 'buyer@rhymio.test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Buyer Street, Quezon City', '09280000000', 'buyer', 1, NULL
WHERE NOT EXISTS (
    SELECT id FROM users WHERE email = 'buyer@rhymio.test'
);
