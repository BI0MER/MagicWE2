<?php

declare(strict_types=1);

namespace xenialdan\MagicWE2\helper;

use BadFunctionCallException;
use jackmd\scorefactory\ScoreFactory;
use OutOfBoundsException;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat as TF;
use ReflectionClass;
use ReflectionException;
use xenialdan\MagicWE2\API;
use xenialdan\MagicWE2\clipboard\SingleClipboard;
use xenialdan\MagicWE2\Loader;
use xenialdan\MagicWE2\selection\Selection;
use xenialdan\MagicWE2\session\UserSession;

class Scoreboard
{
	public function handleScoreboard(UserSession $session): void
	{
		$player = $session->getPlayer();
		if ($session->isSidebarEnabled()) {
			ScoreFactory::setScore($player, Loader::PREFIX . TF::BOLD . TF::LIGHT_PURPLE . "Панель");
			try {
				if ($session->getLatestSelection() !== null) {
					$line = 0;
					$selection = $session->getLatestSelection();
					ScoreFactory::setScoreLine($player, ++$line, TF::GOLD . $session->getLanguage()->translateString("spacer", ["Выделение"]));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Позиция 1: " . TF::RESET . API::vecToString($selection->getPos1()->asVector3()));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Позиция 2: " . TF::RESET . API::vecToString($selection->getPos2()->asVector3()));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Мир: " . TF::RESET . $selection->getWorld()->getFolderName());
					if ($selection->shape === null)
						ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Фигура: " . TF::RESET . 'не определено');
					else
						ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Фигура: " . TF::RESET . (new ReflectionClass($selection->shape))->getShortName());
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Размер: " . TF::RESET . API::vecToString(new Vector3($selection->getSizeX(), $selection->getSizeY(), $selection->getSizeZ())) . " ({$selection->getShape()->getTotalCount()})");

					ScoreFactory::setScoreLine($player, ++$line, TF::GOLD . $session->getLanguage()->translateString("spacer", ["Настройки"]));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Диапазон: " . TF::RESET . Loader::getInstance()->getToolDistance());
					$editLimit = Loader::getInstance()->getEditLimit();
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Лимит: " . TF::RESET . ($editLimit === -1 ? API::boolToString(false) : $editLimit));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Топорик: " . TF::RESET . API::boolToString($session->isWandEnabled()));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Отладка: " . TF::RESET . API::boolToString($session->isDebugToolEnabled()));
					ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " WAILA: " . TF::RESET . API::boolToString($session->isWailaEnabled()));

					if (($cb = $session->getCurrentClipboard()) instanceof SingleClipboard) {
						ScoreFactory::setScoreLine($player, ++$line, TF::GOLD . $session->getLanguage()->translateString("spacer", ["Вставка"]));
						/** @var SingleClipboard $cb */
						if ($cb->customName !== "")
							ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Имя: " . TF::RESET . $cb->customName);
						if ($cb->selection instanceof Selection) {
							if ($cb->selection->shape === null)
								ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Фигура: " . TF::RESET . 'не определено');
							else
								ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Фигура: " . TF::RESET . (new ReflectionClass($cb->selection->shape))->getShortName());
							ScoreFactory::setScoreLine($player, ++$line, TF::BOLD . " Размер: " . TF::RESET . API::vecToString(new Vector3($cb->selection->getSizeX(), $cb->selection->getSizeY(), $cb->selection->getSizeZ())) . " ({$cb->getTotalCount()})");
						}
					}
					//todo current block palette, schematics, brushes
				}
			} catch (BadFunctionCallException | OutOfBoundsException | ReflectionException $e) {
			}
		}
	}
}