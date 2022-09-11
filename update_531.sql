ALTER TABLE wcf1_jcoins_voucher ADD redeemLimitStart INT(10) NOT NULL DEFAULT 0;
UPDATE wcf1_jcoins_voucher SET redeemLimitStart = redeemLimit;
