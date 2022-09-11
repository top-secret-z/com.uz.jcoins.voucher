DROP TABLE IF EXISTS wcf1_jcoins_voucher;
CREATE TABLE wcf1_jcoins_voucher (
	voucherID				INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	
	title					VARCHAR(80) NOT NULL,
	isBlocked				TINYINT(1) NOT NULL DEFAULT 0,
	isDisabled				TINYINT(1) NOT NULL DEFAULT 1,
	isExpired				TINYINT(1) NOT NULL DEFAULT 0,
	isPublished				TINYINT(1) NOT NULL DEFAULT 0,
	
	notify					TINYINT(1) NOT NULL DEFAULT 1,
	publicationStatus		TINYINT(1) NOT NULL DEFAULT 1,
	publicationDate			INT(10) NOT NULL DEFAULT 0,
	nextDate				INT(10) NOT NULL DEFAULT 0,
	
	expirationStatus		TINYINT(1) NOT NULL DEFAULT 0,
	expirationDate			INT(10) NOT NULL DEFAULT 0,
	period					INT(10) NOT NULL DEFAULT 0,
	periodUnit				VARCHAR(10),
	
	changeTime				INT(10) NOT NULL DEFAULT 0,
	time					INT(10) NOT NULL DEFAULT 0,
	
	userID					INT(10),
	username				VARCHAR(255) NOT NULL DEFAULT '',
	
	typeID					INT(10),
	typeDes					VARCHAR(20),
	codeNumber				INT(10) NOT NULL DEFAULT 1,
	codeRedeemLimit			INT(10) NOT NULL DEFAULT 1,
	codeUserLimit			INT(10) NOT NULL DEFAULT 1,
	codePrefix				VARCHAR(5) NOT NULL DEFAULT '',
	
	isMultilingual			TINYINT(1) NOT NULL DEFAULT 0,
	jCoins					INT(10) NOT NULL,
	raffle					TINYINT(1) NOT NULL DEFAULT 0,
	redeemLimit				INT(10) NOT NULL DEFAULT 0,
	redeemLimitStart		INT(10) NOT NULL DEFAULT 0,
	redeemed				INT(10) NOT NULL DEFAULT 0,
	
	KEY (changeTime),
	KEY (jCoins),
	KEY (redeemed)
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_content;
CREATE TABLE wcf1_jcoins_voucher_content (
	contentID				INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	voucherID				INT(10),
	subject					TEXT,
	content					TEXT,
	footer					TEXT,
	hasEmbeddedObjects		TINYINT(1) NOT NULL DEFAULT 0,
	imageID					INT(10),
	languageID				INT(10),
	
	UNIQUE KEY (voucherID, languageID)
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_to_user;
CREATE TABLE wcf1_jcoins_voucher_to_user (
	voucherID				INT(10) NOT NULL,
	userID					INT(10) NOT NULL,
	username				VARCHAR(255) NOT NULL DEFAULT '',
	lastDate				INT(10) NOT NULL DEFAULT 0,
	redeemDate				INT(10) NOT NULL DEFAULT 0,
	redeemed				INT(10) NOT NULL DEFAULT 0,
	jCoins					INT(10) NOT NULL DEFAULT 0,
	
	UNIQUE KEY (voucherID, userID)
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_log;
CREATE TABLE wcf1_jcoins_voucher_log (
	logID					INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	voucherID				INT(10),
	title					VARCHAR(80) NOT NULL,
	action					VARCHAR(255) NOT NULL DEFAULT '',
	detail					VARCHAR(255) NOT NULL DEFAULT '',
	typeDes					VARCHAR(20),
	jCoins					INT(10) NOT NULL,
	time					INT(10) NOT NULL DEFAULT 0,
	userID					INT(10),
	username				VARCHAR(255) NOT NULL DEFAULT '',
	
	KEY (title),
	KEY (typeDes),
	KEY (jCoins),
	KEY (time),
	KEY (userID)
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_to_category;
CREATE TABLE wcf1_jcoins_voucher_to_category (
	categoryID				INT(10) NOT NULL,
	voucherID				INT(10) NOT NULL,
	
	PRIMARY KEY (categoryID, voucherID)
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_type;
CREATE TABLE wcf1_jcoins_voucher_type (
	id						INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	typeID					INT(10) NOT NULL DEFAULT 0,
	typeTitle				VARCHAR(30) NOT NULL DEFAULT '',
	packageID				INT(10) NOT NULL,
	period					TINYINT(1) NOT NULL DEFAULT 0,
	raffle					TINYINT(1) NOT NULL DEFAULT 0,
	redeemLimit				TINYINT(1) NOT NULL DEFAULT 0,
	sortOrder				INT(10) NOT NULL,
	
	UNIQUE KEY (typeID),
	KEY (sortOrder)
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_redemption;
CREATE TABLE wcf1_jcoins_voucher_redemption (
	id						INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	time					INT(10) NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS wcf1_jcoins_voucher_to_code;
CREATE TABLE wcf1_jcoins_voucher_to_code (
	voucherID				INT(10) NOT NULL,
	code					VARCHAR(13) NOT NULL DEFAULT '',
	redeemed				INT(10) NOT NULL DEFAULT 0
);

-- foreign keys
-- category
ALTER TABLE wcf1_jcoins_voucher_to_category ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_voucher_to_category ADD FOREIGN KEY (voucherID) REFERENCES wcf1_jcoins_voucher (voucherID) ON DELETE CASCADE;

-- code
ALTER TABLE wcf1_jcoins_voucher_to_code ADD FOREIGN KEY (voucherID) REFERENCES wcf1_jcoins_voucher (voucherID) ON DELETE CASCADE;

-- content
ALTER TABLE wcf1_jcoins_voucher_content ADD FOREIGN KEY (voucherID) REFERENCES wcf1_jcoins_voucher (voucherID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_voucher_content ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE SET NULL;
ALTER TABLE wcf1_jcoins_voucher_content ADD FOREIGN KEY (imageID) REFERENCES wcf1_media (mediaID) ON DELETE SET NULL;

-- log
ALTER TABLE wcf1_jcoins_voucher_log ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_jcoins_voucher_log ADD FOREIGN KEY (voucherID) REFERENCES wcf1_jcoins_voucher (voucherID) ON DELETE SET NULL;

-- type
ALTER TABLE wcf1_jcoins_voucher_type ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

-- user
ALTER TABLE wcf1_jcoins_voucher_to_user ADD FOREIGN KEY (voucherID) REFERENCES wcf1_jcoins_voucher (voucherID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_voucher_to_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;

-- voucher
ALTER TABLE wcf1_jcoins_voucher ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
