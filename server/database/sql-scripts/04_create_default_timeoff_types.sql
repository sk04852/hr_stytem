INSERT INTO `timeoff_types` (`id`, policy_id, `name`, is_default, allocation_method, accrual_period, accrual_allowance, created_at, updated_at) VALUES
(1, 1, 'Sick Leave','Yes', 'Unlimited', 'Jan-Dec', 5, NOW(), NOW()),
(2, 1, 'Vocation', 'Yes', 'Accrual', 'Jan-Dec', 21, NOW(), NOW());
