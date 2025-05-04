<?php
namespace phpseclib\phpseclib3Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RSANewRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /**
     * @param Expression $node
     */
    public function refactor(Node $node)
    {        
        if (! $node->expr instanceof Assign) {
         	return null;   
        }
        
        if (! $node->expr->expr instanceof New_) {
         	return null;   
        }
        
        if ($this->isObjectType($node->expr->expr->class, new ObjectType('phpseclib\Crypt\RSA'))) {
            return NodeVisitor::REMOVE_NODE;
        }
        
        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Delete direct instantiations of \phpseclib\Crypt\RSA', []);
    }
}