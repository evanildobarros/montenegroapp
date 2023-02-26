<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Configure;

?>
<p>Olá <?php echo h($user->nome); ?></p>
<p>Você está recebendo este email por ter solicitado a recuperação de sua senha de acesso do Painel Administrativo
    do(a) <?php echo Configure::read('Theme.title'); ?> na página "Esqueci minha senha".</p>
<p>Por questões de segurança, para gerar uma nova senha por favor clique no link abaixo. Será solicitado a entrada de
    uma nova senha, anote-a e guarde em um local seguro.</p>
<p><a href="<?php echo $link; ?>" title="Clique aqui" target="_blank">Clique aqui</a></p>
<br>
<p>Atenciosamente</p>
<p>Suporte <?php echo Configure::read('Theme.title'); ?></p>

