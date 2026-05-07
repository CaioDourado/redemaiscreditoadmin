<?php defined('BASEPATH') OR exit('No direct script access allowed');

switch($content):
    case 'index': ?>
            <div class="panel panel-google">
                <div class="panel-heading">Arquivos</div>
                <table class="panel-table table-condensed table-hover" style="margin: 0">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Nome</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($arquivos as $index => $arquivo): ?>
                        <tr>
                            <td class="text-right"><?php echo $index+1; ?></td>
                            <td>
                                <a href="<?php echo base_url().'files/'.$arquivo->nome; ?>"><?php echo $arquivo->nome; ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php break;
endswitch;