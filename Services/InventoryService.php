<?php

namespace FLApp\Services;

require_once "PHPImapClient/ImapClient/ImapClientException.php";
require_once "PHPImapClient/ImapClient/ImapConnect.php";
require_once "PHPImapClient/ImapClient/ImapClient.php";
require_once "PHPImapClient/ImapClient/IncomingMessage.php";
require_once "PHPImapClient/ImapClient/IncomingMessageAttachment.php";
require_once "PHPImapClient/ImapClient/TypeAttachments.php";
require_once "PHPImapClient/ImapClient/TypeBody.php";
require_once "PHPImapClient/ImapClient/Section.php";
require_once "PHPImapClient/ImapClient/SubtypeBody.php";


use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;

class InventoryService {

	public static function findSnapshot($host, $user, $pass, $temp_folder) {
		// Open connection
		try{
		    $imap = new Imap($host, $user, $pass, Imap::ENCRYPT_TLS);
		    // You can also check out example-connect.php for more connection options.
		}catch (ImapClientException $error){
		    echo $error->getMessage().PHP_EOL;
		    die();
		}

		$imap->selectFolder('INBOX');

		/*/ Get all of the folders as an array of strings
		$folders = $imap->getFolders();
		foreach($folders as $foldername => $folder) {
		    if ($foldername == "INBOX") {
		    	foreach ($folder as $subfoldername => $subfolder) {
				    print_r($subfoldername) ;
		    	}
		    }
		}
		*/

		// Count the messages in the current folder
		$overallMessages = $imap->countMessages();
		$unreadMessages = $imap->countUnreadMessages();
//echo $unreadMessages . " unread" . PHP_EOL;
//echo $overallMessages . " total" . PHP_EOL;
		// Fetch all of the emails in the current folder
		$emails = $imap->getMessages(10, 0, "DESC");

		foreach ($emails as $email) {
			if (stripos($email->header->subject, "snapshot") !== FALSE) {
				//echo count($email->attachments) . " attchments";
				if (count($email->attachments) == 0) {
					//var_dump($email);
					continue;
				}
				file_put_contents($temp_folder . DS . "tmp.xlsx", base64_decode($email->attachments[0]->info->body));
				return true;
			}
		}

		return false;
	}

	public static function processFile($temp_folder) {
		$productsInv = [];
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$reader->setReadDataOnly(true);
		$reader->setLoadSheetsOnly( array("Shipping Performance") );
		$spreadsheet = $reader->load($temp_folder . DS . "tmp.xlsx");
		unlink($temp_folder . DS . "tmp.xlsx");

		$worksheet = $spreadsheet->getActiveSheet();
		$inProducts = false;
		$nameCell = 0;
		foreach ($worksheet->getRowIterator() as $row) {
		    $cellIterator = $row->getCellIterator();
		    $cellIterator->setIterateOnlyExistingCells(FALSE); 
		    $cellNum = 0;
		    $product = [];
		    foreach ($cellIterator as $cell) {
		    	if ($inProducts) {
		    		if ($cellNum == $nameCell) {
		    			$product[0] = $cell->getValue();
		    		} else if ($cellNum == $nameCell + 1) {
		    			$product[1] = $cell->getValue();
					    $productsInv[] = $product;
					    break;
		    		}
		    	} else if ($cell->getValue() == "Product Name") {
		        	$inProducts = true;
		        	$nameCell = $cellNum;
		        	break;
		        } 
		        $cellNum++;
		    }
		}

		return $productsInv;
	}

}