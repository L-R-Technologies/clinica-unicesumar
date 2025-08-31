@extends('layouts.base')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Política de Privacidade - LGPD</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="text-muted">
                            <strong>Última atualização:</strong> {{ date('d/m/Y') }}
                        </p>
                    </div>

                    <h3>1. Quem somos</h3>
                    <p>
                        A Clínica Unicesumar é uma instituição de saúde comprometida com a proteção dos dados pessoais
                        de nossos pacientes, em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018 - LGPD).
                    </p>

                    <h3>2. Dados Coletados</h3>
                    <p>Coletamos e tratamos os seguintes dados pessoais:</p>
                    <ul>
                        <li><strong>Dados de identificação:</strong> Nome completo, CPF, RG</li>
                        <li><strong>Dados de contato:</strong> E-mail, telefone, endereço completo</li>
                        <li><strong>Dados pessoais:</strong> Data de nascimento, sexo, etnia</li>
                        <li><strong>Dados de saúde:</strong> Informações médicas relevantes para o atendimento</li>
                    </ul>

                    <h3>3. Finalidade do Tratamento</h3>
                    <p>Seus dados pessoais são utilizados para:</p>
                    <ul>
                        <li>Prestação de serviços de saúde e atendimento médico</li>
                        <li>Agendamento de consultas e exames</li>
                        <li>Comunicação sobre tratamentos e resultados</li>
                        <li>Cumprimento de obrigações legais e regulamentares</li>
                        <li>Melhoria dos serviços prestados</li>
                    </ul>

                    <h3>4. Base Legal</h3>
                    <p>O tratamento dos seus dados pessoais está fundamentado nas seguintes bases legais:</p>
                    <ul>
                        <li><strong>Consentimento:</strong> Para dados não essenciais ao atendimento</li>
                        <li><strong>Execução de contrato:</strong> Para prestação dos serviços de saúde</li>
                        <li><strong>Tutela da saúde:</strong> Para procedimentos de saúde (Art. 11, LGPD)</li>
                        <li><strong>Cumprimento de obrigação legal:</strong> Conforme exigências do CFM e outros órgãos</li>
                    </ul>

                    <h3>5. Compartilhamento de Dados</h3>
                    <p>Seus dados podem ser compartilhados com:</p>
                    <ul>
                        <li>Profissionais de saúde envolvidos no seu atendimento</li>
                        <li>Laboratórios e clínicas parceiras</li>
                        <li>Planos de saúde e convênios médicos</li>
                        <li>Autoridades competentes, quando exigido por lei</li>
                    </ul>
                    <p><strong>Importante:</strong> Não vendemos, alugamos ou transferimos seus dados para terceiros para fins comerciais.</p>

                    <h3>6. Segurança dos Dados</h3>
                    <p>Implementamos medidas técnicas e organizacionais adequadas para proteger seus dados contra:</p>
                    <ul>
                        <li>Acesso não autorizado</li>
                        <li>Alteração, destruição ou perda acidental</li>
                        <li>Tratamento ilícito ou inadequado</li>
                    </ul>

                    <h3>7. Retenção de Dados</h3>
                    <p>
                        Seus dados serão mantidos pelo período necessário para as finalidades descritas,
                        respeitando os prazos legais de guarda de prontuários médicos (mínimo de 20 anos após
                        o último atendimento, conforme Resolução CFM nº 1.821/2007).
                    </p>

                    <h3>8. Seus Direitos</h3>
                    <p>Você tem os seguintes direitos em relação aos seus dados pessoais:</p>
                    <ul>
                        <li><strong>Confirmação e acesso:</strong> Saber se tratamos seus dados e ter acesso a eles</li>
                        <li><strong>Correção:</strong> Corrigir dados incompletos, inexatos ou desatualizados</li>
                        <li><strong>Anonimização, bloqueio ou eliminação:</strong> De dados desnecessários ou excessivos</li>
                        <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
                        <li><strong>Revogação do consentimento:</strong> Quando aplicável</li>
                        <li><strong>Informação sobre compartilhamento:</strong> Saber com quem seus dados são compartilhados</li>
                    </ul>

                    <h3>9. Como Exercer Seus Direitos</h3>
                    <p>Para exercer seus direitos ou esclarecer dúvidas sobre esta política, entre em contato conosco:</p>
                    <ul>
                        <li><strong>E-mail:</strong> privacidade@clinicaunicesumar.edu.br</li>
                        <li><strong>Telefone:</strong> (41) 3389-7000</li>
                        <li><strong>Endereço:</strong> R. Itajubá, 673 - Portão, Curitiba - PR, 81070-190</li>
                    </ul>

                    <h3>10. Encarregado de Dados (DPO)</h3>
                    <p>
                        Nossa Encarregada de Proteção de Dados é responsável por orientar funcionários e contractors
                        sobre as práticas a serem tomadas em relação à proteção de dados pessoais.
                    </p>
                    <p><strong>Contato do DPO:</strong> dpo@clinicaunicesumar.edu.br</p>

                    <h3>11. Alterações nesta Política</h3>
                    <p>
                        Esta política pode ser atualizada periodicamente. Recomendamos que você a revise regularmente.
                        Alterações significativas serão comunicadas previamente.
                    </p>

                    <h3>12. Autoridade Nacional de Proteção de Dados (ANPD)</h3>
                    <p>
                        Se não conseguirmos resolver suas preocupações, você pode entrar em contato com a ANPD através
                        do site: <a href="https://www.gov.br/anpd" target="_blank">www.gov.br/anpd</a>
                    </p>

                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-outline-secondary">Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, nav, footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
