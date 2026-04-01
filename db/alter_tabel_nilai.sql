ALTER TABLE `nilai` DROP PRIMARY KEY;
ALTER TABLE nilai 
ADD UNIQUE KEY unik (id_siswa, id_ujian);
