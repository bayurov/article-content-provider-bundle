<?php

namespace CatCasCarSkillboxSymfony\ArticleContentProviderBundle;

use CatCasCarSkillboxSymfony\ArticleContentProviderBundle\Event\OnBeforeWordPasteEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class ArticleContentProvider
{
    private $paragraphs = [
        'Lorem ipsum **красная точка** dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt [Сметанка](/) ut labore et dolore magna aliqua.',
        'Peppermint tea soup is just not the same without thyme and aged whole avocados. The justice of your __awarenesses__ will balance wonderfully when you respect that enlightenment is the monkey.',
        'When the wench laughs for [east india](/), all cannons fight golden, addled cockroachs. Yell without sonic shower, and we won’t love a particle.',
        'What’s the secret to cored and roasted celery? Always use sichuan-style curry. **Jolly** there\'s nothing like the undead malaria growing on the sail.',
        'Cum __pars__ assimilant, omnes dominaes attrahendam bi-color, salvus scutumes. Yes, there is spiritual places, it empowers with enlightenment.',
    ];

    private bool $isBold;
    private WordDecoratorInterface $decorator;
    private ?EventDispatcherInterface $dispatcher;
    
    public function __construct(
        WordDecoratorInterface $decorator,
        ?EventDispatcherInterface $dispatcher,
        bool $isBold = true,
    ) {
        $this->isBold = $isBold;
        $this->decorator = $decorator;
        $this->dispatcher = $dispatcher;
    }

    public function get(int $paragraphs, string $word = null, int $wordsCount = 0): string
    {
        $texts = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            $texts[] = $this->paragraphs[rand(0, count($this->paragraphs) - 1)];
        }
        
        $text = implode(PHP_EOL . PHP_EOL, $texts);
        
        if ($word && $wordsCount) {
            $text = $this->paste($text, $word, $wordsCount);
        }
        
        return $text;
    }

    private function paste(string $text, string $word, int $wordsCount = 1): string
    {
        $words = explode(' ', $text);

        for ($i = 0; $i < $wordsCount; $i++) {
            $count = count($words);

            $position = rand(0, $count - 1);
    
            $event = new OnBeforeWordPasteEvent($word, $position);
            $this->dispatcher?->dispatch($event);
            
            array_splice($words, $position, 0,  $this->decorator->decorate($event->getWord()));
        }

        return implode(' ', $words);
    }

}
