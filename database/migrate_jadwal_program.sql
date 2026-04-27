-- ============================================================
-- Migration: Add waktu_mulai / waktu_selesai to jadwal table
-- Run this once in your database to support the new time picker
-- ============================================================

-- Add time columns if not exist
ALTER TABLE jadwal
    ADD COLUMN IF NOT EXISTS waktu_mulai   TIME DEFAULT NULL AFTER hari,
    ADD COLUMN IF NOT EXISTS waktu_selesai TIME DEFAULT NULL AFTER waktu_mulai;

-- Backfill keterangan from existing rows if keterangan is empty
UPDATE jadwal SET keterangan = CONCAT(hari, ', ', COALESCE(waktu,'')) 
WHERE keterangan IS NULL OR keterangan = '';

-- Make sure is_active column exists (may already be there)
ALTER TABLE jadwal
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1;

-- Same for program table
ALTER TABLE program
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1;
