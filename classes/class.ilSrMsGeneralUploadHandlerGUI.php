<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

use ILIAS\FileUpload\Handler\AbstractCtrlAwareUploadHandler;
use ILIAS\FileUpload\Handler\FileInfoResult;
use ILIAS\FileUpload\Handler\HandlerResult;
use ILIAS\FileUpload\Handler\BasicHandlerResult;
use ILIAS\FileUpload\Handler\BasicFileInfoResult;
use srag\Plugins\SrMemberships\Container\Init;

/**
 * @ilCtrl_IsCalledBy ilSrMsGeneralUploadHandlerGUI: ilUIPluginRouterGUI
 */
class ilSrMsGeneralUploadHandlerGUI extends AbstractCtrlAwareUploadHandler
{
    /**
     * @var \ILIAS\ResourceStorage\Services
     */
    private $irss;
    /**
     * @var ilSrMsStakeholder
     */
    private $stakeholder;

    public function __construct()
    {
        parent::__construct();
        global $DIC;
        $container = Init::init($DIC);
        $this->irss = $container->dic()->resourceStorage();
        $this->stakeholder = new ilSrMsStakeholder();
    }

    protected function getUploadResult() : HandlerResult
    {
        if (!$this->upload->hasBeenProcessed()) {
            $this->upload->process();
        }

        $upload_results = $this->upload->getResults();

        if (count($upload_results) > 1) {
            return new BasicHandlerResult(
                self::DEFAULT_FILE_ID_PARAMETER,
                HandlerResult::STATUS_FAILED,
                '',
                'one file only allowed'
            );
        }
        $result = reset($upload_results);
        if (!$result->isOK()) {
            return new BasicHandlerResult(
                self::DEFAULT_FILE_ID_PARAMETER,
                HandlerResult::STATUS_FAILED,
                '',
                $result->getStatus()->getMessage()
            );
        }
        $rid = $this->irss->manage()->upload(
            $result,
            $this->stakeholder
        );

        return new BasicHandlerResult(
            self::DEFAULT_FILE_ID_PARAMETER,
            HandlerResult::STATUS_OK,
            $rid->serialize(),
            ''
        );
    }

    protected function getRemoveResult(string $identifier) : HandlerResult
    {
        return new BasicHandlerResult(
            $identifier,
            HandlerResult::STATUS_OK,
            self::DEFAULT_FILE_ID_PARAMETER,
            'removed'
        );
    }

    protected function getInfoResult(string $identifier) : FileInfoResult
    {
        $rid = $this->irss->manage()->find($identifier);
        if ($rid === null) {
            return new BasicFileInfoResult(
                self::DEFAULT_FILE_ID_PARAMETER,
                $identifier,
                'filename',
                0,
                'mime'
            );
        }
        $revision = $this->irss->manage()->getCurrentRevision($rid);

        return new BasicFileInfoResult(
            self::DEFAULT_FILE_ID_PARAMETER,
            $identifier,
            $revision->getTitle(),
            $revision->getInformation()->getSize(),
            $revision->getInformation()->getMimeType()
        );
    }

    protected function getClassStack() : array
    {
        return [ilUIPluginRouterGUI::class, self::class];
    }

    public function getInfoForExistingFiles(array $file_ids) : array
    {
        return [];
    }

    public function getUploadURL() : string
    {
        return $this->ctrl->getLinkTargetByClass($this->getClassStack(), self::CMD_UPLOAD);
    }

    /**
     * @inheritDoc
     */
    public function getExistingFileInfoURL() : string
    {
        return $this->ctrl->getLinkTargetByClass($this->getClassStack(), self::CMD_INFO);
    }

    /**
     * @inheritDoc
     */
    public function getFileRemovalURL() : string
    {
        return $this->ctrl->getLinkTargetByClass($this->getClassStack(), self::CMD_REMOVE);
    }
}
