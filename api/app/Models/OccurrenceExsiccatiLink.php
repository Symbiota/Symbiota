<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccurrenceExsiccatiLink extends Model{

	protected $table = 'omexsiccatiocclink';
	protected $primaryKey = 'omexid';
	public $timestamps = false;

	protected $fillable = [];
	protected $visible = [];
	protected $hidden = [];

	public function number(){
		return $this->hasOne(OccurrenceExsiccatiNumber::class, 'omenid', 'omenid');
	}

	public function occurrence(){
		return $this->hasOne(Occurrence::class, 'occid', 'occid');
	}

	/*
	 *
	 ALTER TABLE `omexsiccatititles`
	   CHANGE COLUMN `exsrange` `exsRange` VARCHAR(45) NULL DEFAULT NULL ,
	   CHANGE COLUMN `startdate` `startDate` VARCHAR(45) NULL DEFAULT NULL ,
	   CHANGE COLUMN `enddate` `endDate` VARCHAR(45) NULL DEFAULT NULL ,
	   CHANGE COLUMN `lasteditedby` `lastEditedBy` VARCHAR(45) NULL DEFAULT NULL ,
	   CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

	 ALTER TABLE `omexsiccatititles`
	   DROP INDEX `index_exsiccatiTitle`;
	 ALTER TABLE `omexsiccatititles`
	   ADD INDEX `IX_exsiccatiTitle` (`title` ASC);


	 ALTER TABLE `omexsiccatinumbers`
	   CHANGE COLUMN `exsnumber` `exsNumber` VARCHAR(45) NOT NULL ,
	   CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

	 ALTER TABLE `omexsiccatinumbers`
	   ADD UNIQUE INDEX `UQ_exsiccatiNumbers_exsNumber_ometid` (`exsNumber` ASC, `ometid` ASC),
	   ADD INDEX `IX_exsiccatiNumbers_ometid_fk` (`ometid` ASC);

	 ALTER TABLE `omexsiccatinumbers`
	   DROP INDEX `Index_omexsiccatinumbers_unique`,
	   DROP INDEX `FK_exsiccatiTitle`,
	   DROP INDEX `FK_exsiccatiTitleNumber`;


	 ALTER TABLE `omexsiccatiocclink`
	   ADD COLUMN `omexid` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
	   CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
	   DROP PRIMARY KEY,
	   ADD PRIMARY KEY (`omexid`);

	 ALTER TABLE `omexsiccatiocclink`
	   ADD UNIQUE INDEX `UQ_exsiccati_occid` (`occid` ASC),
	   ADD INDEX `IX_exsiccati_omenid_fk` (`omenid` ASC),
	   ADD INDEX `IX_exsiccati_occid_fk` (`occid` ASC);

	 ALTER TABLE `omexsiccatiocclink`
	   DROP INDEX `UniqueOmexsiccatiOccLink`,
	   DROP INDEX `FKExsiccatiNumOccLink1`,
	   DROP INDEX `FKExsiccatiNumOccLink2`;

	ALTER TABLE `specprocessorrawlabels`
	  CHANGE COLUMN `rawstr` `rawStr` TEXT NOT NULL ,
	  CHANGE COLUMN `processingvariables` `processingVariables` VARCHAR(250) NULL DEFAULT NULL ,
	  CHANGE COLUMN `sortsequence` `sortSequence` INT(11) NULL DEFAULT NULL ,
	  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;


	 */


}
