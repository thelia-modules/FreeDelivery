<?php

namespace FreeDelivery\Controller;

use FreeDelivery\FreeDelivery;
use FreeDelivery\Model\FreeDeliveryConditionQuery;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/freedelivery/save", name="freedelivery_save")
 */
class ConfigurationController extends BaseAdminController
{
    /**
     * @Route("", name="", methods="POST")
     */
    public function saveAction(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        try {
            $useTaxes = $request->request->get("useTaxes");
            if ($useTaxes == "yes" || $useTaxes == "no") {
                FreeDelivery::setConfigValue('freedelivery_use_tax', $useTaxes);
            }
            $data = $request->request->all();


            if (isset($data['amounts'])) {
                $moduleKeyPrefix = "module_";
                $areaKeyPrefix = "area_";
                foreach ($data['amounts'] as $moduleKey => $areaArray) {
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
                                throw new \Exception(Translator::getInstance()->trans(
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
            return new JsonResponse($e->getMessage(), 500);
        }
        return new JsonResponse("Success");
    }
}
