CREATE TABLE `contract` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `business_name` VARCHAR(255) NOT NULL,
    `nip` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL
);

INSERT INTO `contract` (`id`, `business_name`, `nip`, `amount`) VALUES
    (1, 'Jan Kowalski', '123', 100),
    (2, 'Anna Nowak', '1234', 200.50),
    (3, 'Piotr Wiśniewski', '12345', 35.99),
    (4, 'Anna Wiśniewska', '123456', 9);
    