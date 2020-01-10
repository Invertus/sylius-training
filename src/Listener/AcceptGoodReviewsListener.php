<?php

declare(strict_types=1);

namespace App\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class AcceptGoodReviewsListener
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $productReviewManager;

    /** @var Session */
    private $session;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        ObjectManager $productReviewManager,
        SessionInterface $session
    )
    {
        Assert::isInstanceOf($session, Session::class);

        $this->stateMachineFactory = $stateMachineFactory;
        $this->productReviewManager = $productReviewManager;
        $this->session = $session;
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /** @var ReviewInterface $productReview */
        $productReview = $event->getSubject();

        if ($productReview->getRating() < 4) {
            return;
        }

        $stateMachine = $this->stateMachineFactory->get($productReview, ProductReviewTransitions::GRAPH);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_ACCEPT);

        $this->productReviewManager->persist($productReview);
        $this->productReviewManager->flush();

        $flashBag = $this->session->getFlashBag();
        $flashBag->get('success');
        $flashBag->add('success', 'Your review has been automatically approved.');
    }
}
