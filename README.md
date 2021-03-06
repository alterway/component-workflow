# Workflow Component [![Build Status](https://travis-ci.org/alterway/component-workflow.png?branch=master)](https://travis-ci.org/alterway/component-workflow)

This component provides a workflow engine written as a PHP library.

Instead of modeling a workflow as a Petri net or trying to enumerate workflow patterns, the library consider a workflow as a simple directed graph: vertices model nodes and edges model transitions.

### Nodes

A node represents a point in a life cycle.
The `Node` class implements the concept.
A node is referenced by a unique name across the workflow.
The constraint is the responsibility of `NodeMap` class.

### Transitions

A transition is a link between nodes.
The `Transition` class implements the concept.
At creation, a transition is given a specification object implementing the `SpecificationInterface`.
the specification is used as a business rule to decide where to advance in the workflow.

### Tokens

A token is a simple string used to initialize the workflow in a particular node.
The idea is to consider the token as a thing placed at the center of a node.
When workflow engine is on, the token is moving from node to node.

### Events

An event is an object created each time a token arrives at a node.
The `Event` class implements the concept.
This class extends the `Event` class from the Symfony EventDispatcher component.
You can write listeners or subscribers to implement any business behaviour.

## Usage

Let's say you are writing a blog engine in PHP and you want to implement the following workflow:
* an article begins its existence as a draft
* when ready, the article gets published
* if controversial, the article is deleted
* when too old, the article is archived

First of all, you need to write classes implementing `SpecificationInterface` for every business rule:
```php
namespace BlogEngine\Domain\Specification;

use Alterway\Component\Workflow\ContextInterface;
use Alterway\Component\Workflow\SpecificationInterface;

class DraftableArticleSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(ContextInterface $context)
    {
        // an article can always be drafted
        return true;
    }
}

class PublishableArticleSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(ContextInterface $context)
    {
        // an article needs two reviews to be published
        return 1 < count($context->get('article')->getReviews());
    }
}

class DeletableArticleSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(ContextInterface $context)
    {
        // an article can always be deleted if requested
        return 'delete' === $context->get('action');
    }
}

class ArchivableArticleSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(ContextInterface $context)
    {
        // an article needs to be one month old to be archived
        $publishedAtPlusOneMonth = clone $context->get('publishedAt');
        $publishedAtPlusOneMonth->modify('+1 month');

        return 'archive' === $context->get('action') && $publishedAtPlusOneMonth < $context->get('now');
    }
}
```

Then, you can use the `Builder` class and the specifications to describe the workflow:
```php
namespace BlogEngine\Domain\Service;

use Alterway\Component\Workflow\Builder;
use Alterway\Component\Workflow\ContextInterface;
use BlogEngine\Domain\Event\ArticleSubscriber;
use BlogEngine\Domain\Specification\DraftableArticleSpecification;
use BlogEngine\Domain\Specification\PublishableArticleSpecification;
use BlogEngine\Domain\Specification\DeletableArticleSpecification;
use BlogEngine\Domain\Specification\ArchivableArticleSpecification;
use BlogEngine\Util\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticleService
{
    private $workflow;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->workflow = (new Builder($eventDispatcher))
            ->open('article.draft', new DraftableArticleSpecification())
            ->link('article.draft', 'article.published', new PublishableArticleSpecification())
            ->link('article.published', 'article.deleted', new DeletableArticleSpecification())
            ->link('article.published', 'article.archived', new ArchivableArticleSpecification())
            ->getWorkflow();

        $eventDispatcher->addSubscriber(new ArticleSubscriber());
    }

    public function create(Article $article)
    {
        $this->advance($article, new Context());
    }

    public function publish(Article $article)
    {
        $context = new Context();
        $context->set('article', $article);

        $this->advance($article, $context);
    }

    public function delete(Article $article)
    {
        $context = new Context();
        $context->set('action', 'delete');

        $this->advance($article, $context);
    }

    public function archive(Article $article)
    {
        $context = new Context();
        $context->set('action', 'archive');
        $context->set('publishedAt', $article->getPublishedAt());
        $context->set('now', new \DateTime());

        $this->advance($article, $context);
    }

    private function advance($article, ContextInterface $context)
    {
        try {
            $this->workflow->initialize($article->getToken())->next($context);
        } catch (\LogicException $e) {
            // the workflow reports a problem
        }
    }
}
```

Finally, you have to listen on events dispatched by the workflow to attach the business behavior:
```php
namespace BlogEngine\Domain\Event;

use Alterway\Component\Workflow\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'article.draft' => array('onDraft', 0),
            'article.published' => array('onPublished', 0),
            'article.deleted' => array('onDeleted', 0),
            'article.archived' => array('onArchived', 0),
        );
    }

    public function onDraft(Event $event) { /* ... */ }

    public function onPublished(Event $event) { /* ... */ }

    public function onDeleted(Event $event) { /* ... */ }

    public function onArchived(Event $event) { /* ... */ }
}
```

## Contributing

Pretty please, with sugar on top, phpspec specifications are provided and should be green when contributing code.

## References

### Theory

* [Petri net](http://en.wikipedia.org/wiki/Petri_net)
* [Workflow patterns](http://www.workflowpatterns.com/)
* [Graph theory](http://en.wikipedia.org/wiki/Graph_theory)
* [Specification pattern](http://en.wikipedia.org/wiki/Specification_pattern)

### PHP

* [An activity based workflow engine](http://www.tonymarston.net/php-mysql/workflow.html)
* [eZ Workflow component](http://www.ezcomponents.org/docs/api/latest/introduction_Workflow.html)
* [Yii simpleWorkflow extension](http://www.yiiframework.com/extension/simpleworkflow/)
* [Galaxia workflow engine](http://workflow.tikiwiki.org/tiki-index.php?page=homepage)
* [State pattern by Sebastian Bergmann](https://github.com/sebastianbergmann/state)
* [Petrinet Framework](https://github.com/florianv/petrinet)

## Licencing

See the bundled LICENSE file for details.

## Sponsors

* [Alter Way](http://www.alterway.fr)
* [La Ruche Qui Dit Oui !](http://www.laruchequiditoui.fr)
