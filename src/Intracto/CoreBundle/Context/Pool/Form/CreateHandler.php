<?php

namespace Intracto\CoreBundle\Context\Pool\Form;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateHandler.
 */
class CreateHandler
{
    /**
     * @var FormInterface form
     */
    private $form;

    /**
     * @var FormFactory formFactory
     */
    private $formFactory;

    /**
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    public function handle(Request $request)
    {
        $this->form = $this->createForm();
//        $this->form->handleRequest($request);
//        if ($this->form->isSubmitted()) {
//            if ($this->form->isValid()) {
//                $this->saveData($this->form->getData());
//                $this->eventDispatcher->dispatch(ProductBatchUpdatedEvent::NAME, new ProductBatchUpdatedEvent([$productId]));
//                $this->session->getFlashBag()->add('notice', $this->translator->trans('form.message.success'));
//
//                return new RedirectResponse($this->router->generate('pim.product.form', ['productId' => $productId]));
//            } else {
//                $this->session->getFlashBag()->add('error', $this->translator->trans('form.message.error'));
//            }
//        }
    }


    /**
     * @return FormInterface
     */
    private function createForm()
    {
        $dto = new CreateDto();

        return $this->formFactory->create(CreateType::class, $dto);
    }
}