#!/bin/bash

echo '
INSERT INTO type (id,name,unit,n_sensors) VALUES (1,"Cell","V",46);
-- Primary key int(0) must be set manually through update
UPDATE type SET id = 0 WHERE id = 1;
INSERT INTO type (id,name,unit,n_sensors) VALUES (1,"Sum voltage","V",1);
INSERT INTO type (id,name,unit,n_sensors) VALUES (2,"Temperature","C",12);
INSERT INTO type (id,name,unit,n_sensors) VALUES (3,"Pressure","Pa",2);
INSERT INTO type (id,name,unit,n_sensors) VALUES (4,"Voltage","V",5);

-- Expects 46 Cell voltage sensors, 1 total cell voltage, 12 temperature sensors, 5 Voltage sensors and 2 pressure sensors
'

for i in {0..45}
do
	echo "INSERT INTO type_sensor (type, n, name, min, max) VALUES (0,$i,'Cell $i',0,10);"
done

	echo "INSERT INTO type_sensor (type, n, name, min, max) VALUES (1,$i,'Sum cell $i',20,25);"

for i in {0..11}
do
	echo "INSERT INTO type_sensor (type, n, name, min, max) VALUES (2,$i,'Temperature $i',22,50);"
done

for i in {0..1}
do
	echo "INSERT INTO type_sensor (type, n, name, min, max) VALUES (3,$i,'Pressure $i',0,10);"
done

for i in {0..4}
do
	echo "INSERT INTO type_sensor (type, n, name, min, max) VALUES (4,$i,'Voltage $i',0,10);"
done

for i in {1..6}
do
	echo "INSERT INTO laps (id, planned_time) VALUES ($i,420);"
done
