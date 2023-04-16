<?php

namespace Controllers\Routes;

use Common\Database\User;
use Common\Plan;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Tnapf\Router\Interfaces\RequestHandlerInterface;

use function Common\render;

class Plans implements RequestHandlerInterface
{
    public static function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        stdClass $args,
        callable $next
    ): ResponseInterface {
        $free = new Plan(
            User::PLAN_FREE,
            0,
            "Free",
            "Access to our community forum, where you can ask and answer coding questions. You also have access to all previously asked questions",
            "green",
            [
                "Access to our community forum",
                "Ask and answer coding questions",
                "Connect with other developers",
                "Ask 15 questions a week",
                "Search through all previously asked questions"
            ]
        );

        $boosted = new Plan(
            User::PLAN_BOOSTED,
            20,
            "Boosted",
            "Get your posts in front of more developers with our Boosted Posts plan. Your posts will be promoted to our community, ensuring that more people see them and you get the help you need faster.",
            "orange",
            [
                "All perks of Free plan",
                "Unlimited questions",
                "Priority placement in the community",
                "Boosted posts for maximum visibility"
            ]
        );

        $pro = new Plan(
            User::PLAN_PRO_BOOSTED,
            30,
            "Pro Boost",
            "Get priority placement in our community and enjoy maximum visibility for your posts. Plus, receive personalized support from our expert developers to help you achieve your coding goals faster.",
            "blue",
            [
                "All perks of Boosted plan",
                "Personalized support from our expert developers",
                "Guaranteed response time of 12 hours or less"
            ]
        );

        $plans = compact("free", "boosted", "pro");

        return render("plans", compact("plans"));
    }
}
