-- Cluster Assignment for Batangas Province Offices
-- Date: 2026-03-02
-- This script assigns all LGUs in Batangas to their designated clusters

-- First, create the offices if they don't exist
-- Province entry
INSERT IGNORE INTO offices (office_name, office_type, cluster, status) VALUES
('Province of Batangas', 'PROVINCE', NULL, 'ACTIVE');

-- CLUSTER 1 - Batangas City Area
INSERT IGNORE INTO offices (office_name, office_type, cluster, status) VALUES
('Batangas City', 'CITY', '1', 'ACTIVE'),
('City of Calaca', 'CITY', '1', 'ACTIVE'),
('Balayan', 'MUNICIPALITY', '1', 'ACTIVE'),
('Calatagan', 'MUNICIPALITY', '1', 'ACTIVE'),
('Lemery', 'MUNICIPALITY', '1', 'ACTIVE'),
('Lian', 'MUNICIPALITY', '1', 'ACTIVE'),
('Nasugbu', 'MUNICIPALITY', '1', 'ACTIVE'),
('Taal', 'MUNICIPALITY', '1', 'ACTIVE'),
('Tuy', 'MUNICIPALITY', '1', 'ACTIVE');

-- CLUSTER 2 - Bauan Area
INSERT IGNORE INTO offices (office_name, office_type, cluster, status) VALUES
('Bauan', 'MUNICIPALITY', '2', 'ACTIVE'),
('Ibaan', 'MUNICIPALITY', '2', 'ACTIVE'),
('Lobo', 'MUNICIPALITY', '2', 'ACTIVE'),
('Mabini', 'MUNICIPALITY', '2', 'ACTIVE'),
('Padre Garcia', 'MUNICIPALITY', '2', 'ACTIVE'),
('Rosario', 'MUNICIPALITY', '2', 'ACTIVE'),
('San Luis', 'MUNICIPALITY', '2', 'ACTIVE'),
('San Jose', 'MUNICIPALITY', '2', 'ACTIVE'),
('Taysan', 'MUNICIPALITY', '2', 'ACTIVE'),
('San Juan', 'MUNICIPALITY', '2', 'ACTIVE'),
('San Pascual', 'MUNICIPALITY', '2', 'ACTIVE'),
('Tingloy', 'MUNICIPALITY', '2', 'ACTIVE');

-- CLUSTER 3 - Lipa Area
INSERT IGNORE INTO offices (office_name, office_type, cluster, status) VALUES
('City of Lipa', 'CITY', '3', 'ACTIVE'),
('City of Sto. Tomas', 'CITY', '3', 'ACTIVE'),
('City of Tanauan', 'CITY', '3', 'ACTIVE'),
('Agoncillo', 'MUNICIPALITY', '3', 'ACTIVE'),
('Alitagtag', 'MUNICIPALITY', '3', 'ACTIVE'),
('Balete', 'MUNICIPALITY', '3', 'ACTIVE'),
('Cuenca', 'MUNICIPALITY', '3', 'ACTIVE'),
('Laurel', 'MUNICIPALITY', '3', 'ACTIVE'),
('Malvar', 'MUNICIPALITY', '3', 'ACTIVE'),
('Mataasnakahoy', 'MUNICIPALITY', '3', 'ACTIVE'),
('San Nicolas', 'MUNICIPALITY', '3', 'ACTIVE'),
('Sta Teresita', 'MUNICIPALITY', '3', 'ACTIVE'),
('Talisay', 'MUNICIPALITY', '3', 'ACTIVE');

-- Update existing offices with cluster assignments (in case they already exist)
-- CLUSTER 1
UPDATE offices SET cluster = '1' WHERE office_name IN (
    'Batangas City', 'City of Calaca', 'Balayan', 'Calatagan', 
    'Lemery', 'Lian', 'Nasugbu', 'Taal', 'Tuy'
);

-- CLUSTER 2
UPDATE offices SET cluster = '2' WHERE office_name IN (
    'Bauan', 'Ibaan', 'Lobo', 'Mabini', 'Padre Garcia', 
    'Rosario', 'San Luis', 'San Jose', 'Taysan', 
    'San Juan', 'San Pascual', 'Tingloy'
);

-- CLUSTER 3
UPDATE offices SET cluster = '3' WHERE office_name IN (
    'City of Lipa', 'City of Sto. Tomas', 'City of Tanauan', 
    'Agoncillo', 'Alitagtag', 'Balete', 'Cuenca', 'Laurel', 
    'Malvar', 'Mataasnakahoy', 'San Nicolas', 'Sta Teresita', 'Talisay'
);

-- Verify cluster assignments
SELECT 
    cluster,
    COUNT(*) as office_count,
    GROUP_CONCAT(office_name ORDER BY office_name SEPARATOR ', ') as offices
FROM offices
WHERE cluster IS NOT NULL
GROUP BY cluster
ORDER BY cluster;

-- Summary
SELECT 
    'Total Offices' as description, COUNT(*) as count FROM offices WHERE cluster IS NOT NULL
UNION ALL
SELECT 'Cluster 1', COUNT(*) FROM offices WHERE cluster = '1'
UNION ALL
SELECT 'Cluster 2', COUNT(*) FROM offices WHERE cluster = '2'
UNION ALL
SELECT 'Cluster 3', COUNT(*) FROM offices WHERE cluster = '3';
