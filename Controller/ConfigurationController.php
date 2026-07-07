<?php

declare(strict_types=1);

namespace FreeDelivery\Controller;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Translation\Translator;

class ConfigurationController extends BaseAdminController
{
    protected bool $useFallbackTemplate = true;

    #[Route('/admin/module/freedelivery/save', name: 'freedelivery.admin.save', methods: ['POST'])]
    public function saveAction(Request $request)
    {
        $response = $this->checkAuth([], ['freedelivery'], AccessManager::UPDATE);
        if (null !== $response) {
            return $response;
        }

        $this->checkXmlHttpRequest();

        try {
            $useTaxes = $request->request->get('useTaxes');
            if ('yes' === $useTaxes || 'no' === $useTaxes) {
                FreeDelivery::setConfigValue('freedelivery_use_tax', $useTaxes);
            }

            $amounts = $request->request->all()['amounts'] ?? [];
            $moduleKeyPrefix = 'module_';
            $areaKeyPrefix = 'area_';

            foreach ($amounts as $moduleKey => $areaArray) {
                $moduleId = (int) substr((string) $moduleKey, \strlen($moduleKeyPrefix));

                foreach ($areaArray as $areaKey => $amount) {
                    $areaId = (int) substr((string) $areaKey, \strlen($areaKeyPrefix));

                    $isNumeric = is_numeric($amount);

                    if (!$isNumeric && empty($amount)) {
                        FreeDeliveryConditionQuery::create()
                            ->filterByModuleId($moduleId)
                            ->filterByAreaId($areaId)
                            ->delete();

                        continue;
                    }

                    if (!$isNumeric || $amount < 0) {
                        throw new \Exception(Translator::getInstance()->trans(
                            'Invalid value : %value',
                            ['%value' => $amount],
                            FreeDelivery::DOMAIN_NAME
                        ));
                    }

                    FreeDeliveryConditionQuery::create()
                        ->filterByModuleId($moduleId)
                        ->filterByAreaId($areaId)
                        ->findOneOrCreate()
                        ->setAmount((string) $amount)
                        ->save();
                }
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(
                json_encode(['success' => false, 'message' => $e->getMessage()]),
                500
            );
        }

        return $this->jsonResponse(json_encode(['success' => true]));
    }
}
