<?php

/*********************************************************************
 * This code is licensed under the GPL-3.0 license and is part of a
 * ILIAS plugin developed by sr Solutions ag in Switzerland.
 *
 * https://sr.solutions
 *
 *********************************************************************/

use ILIAS\ResourceStorage\Services;
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
     * @readonly
     */
    private Services $irss;
    /**
     * @readonly
     */
    private ilSrMsStakeholder $stakeholder;

    public function __construct()
    {
        global $DIC;
        parent::__construct();
        $container = Init::init($DIC);
        $this->irss = $container->dic()->resourceStorage();
        $this->stakeholder = new ilSrMsStakeholder();
    }

    protected function getUploadResult(): HandlerResult
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

    protected function getRemoveResult(string $identifier): HandlerResult
    {
        return new BasicHandlerResult(
            $identifier,
            HandlerResult::STATUS_OK,
            self::DEFAULT_FILE_ID_PARAMETER,
            'removed'
        );
    }

    public function getInfoResult(string $identifier): ?FileInfoResult
    {
        $rid = $this->irss->manage()->find($identifier);
        if ($rid === null) {
            return null;
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

    protected function getClassStack(): array
    {
        return [ilUIPluginRouterGUI::class, self::class];
    }

    public function getInfoForExistingFiles(array $file_ids): array
    {
        return [];
    }

    public function getUploadURL(): string
    {
        return $this->ctrl->getLinkTargetByClass($this->getClassStack(), self::CMD_UPLOAD);
    }

    public function getExistingFileInfoURL(): string
    {
        return $this->ctrl->getLinkTargetByClass($this->getClassStack(), self::CMD_INFO);
    }

    public function getFileRemovalURL(): string
    {
        return $this->ctrl->getLinkTargetByClass($this->getClassStack(), self::CMD_REMOVE);
    }
}
