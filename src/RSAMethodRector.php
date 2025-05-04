<?php
namespace phpseclib\phpseclib3Rector;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use Rector\Renaming\ValueObject\MethodCallRename;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/*
This admittedly hackish technique is being employed to facilitate code re-use. RectorPHP defines its
classes as final with private methods so inheritance isn't an option, without this hackish technique

Composition isn't a great option, either, because I'd need to redefine autowire and the other public
methods from AbstractRector. The problem with that is...  what if another parameter is added to
autowire? I'd just assume minimize the time I need to spend maintaining the code - not maximize it.
And plus, if a new parameter were added then it seems unlikely that the code would work on older
versions of Rector. Extended AbstractRector isn't an option because enterNode() is declared final in
AbstractRector and implementing RectorInterface is suboptimal because all the method implementations
in AbstractRector would need to be copied and that kinda contradicts the goal is not duplicating code.
*/
$old = file_get_contents(__DIR__ . '\..\..\..\rector\rector\rules\Renaming\Rector\MethodCall\RenameMethodRector.php');
$tokens = \PhpToken::tokenize($old);
$tokens = array_slice($tokens, 1); // remove the <?php
$new = '';
foreach ($tokens as $token) {
    switch ("$token") {
        case 'final':
            continue 2;
        case 'RenameMethodRector':
            $token = 'NewRenameMethodRector';
            break;
        case 'private':
            $token = 'protected';
    }
    $new.= $token;
}
eval($new);

class RSAMethodRector extends \Rector\Renaming\Rector\MethodCall\NewRenameMethodRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [MethodCall::class, NullsafeMethodCall::class];
    }

    /**
     * @param \PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\NullsafeMethodCall $call
     * @return \PhpParser\Node\Expr\ArrayDimFetch|null|\PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|\PhpParser\Node\Expr\NullsafeMethodCall
     */
    protected function refactorMethodCallAndStaticCall($call)
    {
        $this->methodCallRenames = [
            new MethodCallRename('phpseclib\Crypt\RSA', 'loadKey', 'loadKey'),
            new MethodCallRename('phpseclib\Crypt\RSA', 'createKey', 'createKey'),
            new MethodCallRename('phpseclib\Crypt\RSA', 'setEncryptionMode', 'setEncryptionMode'),
            new MethodCallRename('phpseclib\Crypt\RSA', 'setSignatureMode', 'setSignatureMode'),
        ];
        $call = parent::refactorMethodCallAndStaticCall($call);
        if (is_null($call)) {
            return null;
        }
        switch ($call->name) {
            case 'loadKey':
                return new StaticCall(
                    new FullyQualified('phpseclib3\Crypt\PublicKeyLoader'),
                    'loadKey',
                    $call->args
                );
            case 'createKey':
                return new StaticCall(
                    new FullyQualified('phpseclib3\Crypt\RSA'),
                    'createKey',
                    $call->args
                );
            case 'setEncryptionMode':
            case 'setSignatureMode':
                $className = $call::CLASS;
                $newCall = new $className($call->var, 'withPadding', $call->args);
                return new Assign(
                    $call->var,
                    $newCall
                );
        }
    }

    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Changes method call names for \phpseclib\Crypt\RSA', []);
    }
}