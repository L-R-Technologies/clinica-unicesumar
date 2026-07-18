import { usePage } from '@inertiajs/react';
import { Printer } from 'lucide-react';
import GuestLayout from '@/layouts/guest-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PageProps } from '@/types';

interface Section {
    title: string;
    paragraphs?: string[];
    items?: string[];
}

const SECTIONS: readonly Section[] = [
    {
        title: '1. Quem somos',
        paragraphs: [
            'A Clínica Unicesumar é uma instituição de saúde comprometida com a proteção dos dados pessoais de nossos pacientes, em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018 - LGPD).',
        ],
    },
    {
        title: '2. Dados Coletados',
        paragraphs: ['Coletamos e tratamos os seguintes dados pessoais:'],
        items: [
            'Dados de identificação: Nome completo, CPF, RG',
            'Dados de contato: E-mail, telefone, endereço completo',
            'Dados pessoais: Data de nascimento, sexo, etnia',
            'Dados de saúde: Informações médicas relevantes para o atendimento',
        ],
    },
    {
        title: '3. Finalidade do Tratamento',
        paragraphs: ['Seus dados pessoais são utilizados para:'],
        items: [
            'Prestação de serviços de saúde e atendimento médico',
            'Agendamento de consultas e exames',
            'Comunicação sobre tratamentos e resultados',
            'Cumprimento de obrigações legais e regulamentares',
            'Melhoria dos serviços prestados',
        ],
    },
    {
        title: '4. Base Legal',
        paragraphs: [
            'O tratamento dos seus dados pessoais está fundamentado nas seguintes bases legais:',
        ],
        items: [
            'Consentimento: Para dados não essenciais ao atendimento',
            'Execução de contrato: Para prestação dos serviços de saúde',
            'Tutela da saúde: Para procedimentos de saúde (Art. 11, LGPD)',
            'Cumprimento de obrigação legal: Conforme exigências do CFM e outros órgãos',
        ],
    },
    {
        title: '5. Compartilhamento de Dados',
        paragraphs: ['Seus dados podem ser compartilhados com:'],
        items: [
            'Profissionais de saúde envolvidos no seu atendimento',
            'Laboratórios e clínicas parceiras',
            'Planos de saúde e convênios médicos',
            'Autoridades competentes, quando exigido por lei',
        ],
    },
    {
        title: '6. Segurança dos Dados',
        paragraphs: [
            'Implementamos medidas técnicas e organizacionais adequadas para proteger seus dados contra:',
        ],
        items: [
            'Acesso não autorizado',
            'Alteração, destruição ou perda acidental',
            'Tratamento ilícito ou inadequado',
        ],
    },
    {
        title: '7. Retenção de Dados',
        paragraphs: [
            'Seus dados serão mantidos pelo período necessário para as finalidades descritas, respeitando os prazos legais de guarda de prontuários médicos (mínimo de 20 anos após o último atendimento, conforme Resolução CFM nº 1.821/2007).',
        ],
    },
    {
        title: '8. Seus Direitos',
        paragraphs: [
            'Você tem os seguintes direitos em relação aos seus dados pessoais:',
        ],
        items: [
            'Confirmação e acesso: Saber se tratamos seus dados e ter acesso a eles',
            'Correção: Corrigir dados incompletos, inexatos ou desatualizados',
            'Anonimização, bloqueio ou eliminação: De dados desnecessários ou excessivos',
            'Portabilidade: Receber seus dados em formato estruturado',
            'Revogação do consentimento: Quando aplicável',
            'Informação sobre compartilhamento: Saber com quem seus dados são compartilhados',
        ],
    },
    {
        title: '9. Como Exercer Seus Direitos',
        paragraphs: [
            'Para exercer seus direitos ou esclarecer dúvidas sobre esta política, entre em contato conosco:',
        ],
        items: [
            'E-mail: privacidade@clinicaunicesumar.edu.br',
            'Telefone: (41) 3389-7000',
            'Endereço: R. Itajubá, 673 - Portão, Curitiba - PR, 81070-190',
        ],
    },
    {
        title: '10. Encarregado de Dados (DPO)',
        paragraphs: [
            'Nossa Encarregada de Proteção de Dados é responsável por orientar funcionários e colaboradores sobre as práticas a serem tomadas em relação à proteção de dados pessoais.',
            'Contato do DPO: dpo@clinicaunicesumar.edu.br',
        ],
    },
    {
        title: '11. Alterações nesta Política',
        paragraphs: [
            'Esta política pode ser atualizada periodicamente. Recomendamos que você a revise regularmente. Alterações significativas serão comunicadas previamente.',
        ],
    },
    {
        title: '12. Autoridade Nacional de Proteção de Dados (ANPD)',
        paragraphs: [
            'Se não conseguirmos resolver suas preocupações, você pode entrar em contato com a ANPD através do site: www.gov.br/anpd',
        ],
    },
] as const;

interface PrivacyProps extends Record<string, unknown> {
    updatedAt: string;
}

export default function PrivacyPolicy() {
    const { updatedAt } = usePage<PageProps<PrivacyProps>>().props;

    return (
        <GuestLayout title="Política de Privacidade">
            <div className="mx-auto w-full max-w-3xl px-6 py-10">
                <Card>
                    <CardContent className="prose prose-slate max-w-none dark:prose-invert print:shadow-none">
                        <div className="text-center">
                            <h1 className="text-2xl font-bold">
                                Política de Privacidade - LGPD
                            </h1>
                            <p className="text-sm text-muted-foreground">
                                <strong>Última atualização:</strong> {updatedAt}
                            </p>
                        </div>

                        {SECTIONS.map((section) => (
                            <section key={section.title} className="mt-6">
                                <h2 className="text-lg font-semibold">
                                    {section.title}
                                </h2>
                                {section.paragraphs?.map((paragraph) => (
                                    <p
                                        key={paragraph}
                                        className="mt-2 text-sm text-foreground/90"
                                    >
                                        {paragraph}
                                    </p>
                                ))}
                                {section.items && (
                                    <ul className="mt-2 list-disc space-y-1 pl-6 text-sm text-foreground/90">
                                        {section.items.map((item) => (
                                            <li key={item}>{item}</li>
                                        ))}
                                    </ul>
                                )}
                            </section>
                        ))}

                        <div className="mt-8 text-center print:hidden">
                            <Button onClick={() => window.print()}>
                                <Printer />
                                Imprimir
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </GuestLayout>
    );
}
