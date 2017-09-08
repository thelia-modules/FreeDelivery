<?php

namespace FreeDelivery\Controller;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;

class ConfigurationController extends BaseAdminController
{
    public function saveAction()
    {
        $request = $this->getRequest();

        try {
            $useTaxes = $request->request->get("useTaxes");
            if ($useTaxes == "yes" || $useTaxes == "no") {
                FreeDelivery::setConfigValue('freedelivery_use_tax', $useTaxes);
            }

            $amountsArray = $request->request->get("amounts");
            if (!empty($amountsArray)) {
                $moduleKeyPrefix = "module_";
                $areaKeyPrefix = "area_";
                foreach ($amountsArray as $moduleKey => $areaArray) {
                    $moduleId = substr($moduleKey, strlen($moduleKeyPrefix));
                    foreach ($areaArray as $areaKey => $amount) {
                        $areaId = substr($areaKey, strlen($areaKeyPrefix));

                        $isNumeric = is_numeric($amount);
                        if (!$isNumeric && empty($amount)) {
                            FreeDeliveryConditionQuery::create()
                                ->filterByModuleId($moduleId)
                                ->filterByAreaId($areaId)
                                ->delete();
                        } else {
                            if (!$isNumeric || $amount < 0) {
                                throw new \Exception($this->getTranslator()->trans(
                                    "Invalid value : %value",
                                    [ '%value' => $$amount ]
                                ));
                            }

                            FreeDeliveryConditionQuery::create()
                                ->filterByModuleId($moduleId)
                                ->filterByAreaId($areaId)
                                ->findOneOrCreate()
                                ->setAmount($amount)
                                ->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return JsonResponse::create($e->getMessage(), 500);
        }
        return JsonResponse::create("Success");
    }
}
