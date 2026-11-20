@extends('layouts.master')

@section('title', 'Simulação de segurança')

@section('content')
    <section class="hero security-hero">
        <div>
            <span class="eyebrow">Laboratório defensivo anti-Eggman (no caso de ele tentar conquistar o mundo outra vez
                -_-)</span>
            <h1>Simulação Eggman Hack</h1>
            <p>Demonstração local de proteção contra acessos indevidos e excesso de pedidos.
                Aquele Eggman! Sempre a tentar
                conquistar o mundo!</p>
        </div>
        <span class="security-status">Modo de demonstração</span>
        <img class="security-eggman" src="{{ asset('images/eggman-security.png') }}" alt="Dr. Eggman no laboratório defensivo">
    </section>

    <div class="security-notice" role="note">
        Esta simulação só cria eventos sintéticos na aplicação: não envia pedidos externos nem tenta aceder a recursos
        reais. Não quero que o Eggman magõe os meus amigos!
    </div>
    @include('partials.flash')

    <section class="content-panel">
        <div class="panel-heading">
            <div>
                <h2>Eventos de segurança</h2>
                <p>Registos mais recentes da demonstração defensiva.</p>
            </div>
            <span class="panel-badge panel-badge-blue">{{ $incidents->count() }} eventos</span>
        </div>
        <div class="security-actions">
            <p>Cada execução cria cinco eventos de teste: três ficam dentro do limite e os dois seguintes são
                bloqueados. O botão está limitado a três execuções por minuto. Para ser honesto , mesmo sendo um genio tive
                bastante dificuldade a implementar esta feature , se calhar vou pedir ao Sonic para me ajudar a testar!</p>
            <div class="action-row security-action-buttons">
                <form method="POST" action="{{ route('security.simulate') }}">
                    @csrf
                    <button class="button button-primary" type="submit">Executar simulação segura</button>
                </form>
                <form method="POST" action="{{ route('security.incidents.destroy-all') }}"
                    onsubmit="return confirm('Apagar todos os registos de segurança? Esta ação não pode ser desfeita.')">
                    @csrf
                    @method('DELETE')
                    <button class="button button-primary" type="submit"
                        aria-label="Apagar todos os registos de segurança">Apagar todos</button>
                </form>
            </div>
        </div>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Descrição</th>
                        <th>Tentativa</th>
                        <th>Resultado</th>
                        <th>Registado</th>
                        <th class="align-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($incidents as $incident)
                        <tr>
                            <td><strong>{{ $incident->category }}</strong></td>
                            <td>{{ $incident->summary }}</td>
                            <td>#{{ $incident->attempt }}</td>
                            <td>
                                @if ($incident->blocked)
                                    <span class="security-status security-status-blocked">Bloqueado</span>
                                @else
                                    <span class="security-status">Dentro do limite</span>
                                @endif
                            </td>
                            <td><span class="date-label">{{ $incident->created_at->format('d/m/Y H:i:s') }}</span></td>
                            <td class="align-right">
                                <form class="action-row" method="POST"
                                    action="{{ route('security.incidents.destroy', $incident) }}"
                                    onsubmit="return confirm('Apagar este registo de segurança?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button button-primary button-small" type="submit"
                                        aria-label="Apagar registo de segurança #{{ $incident->id }}">Apagar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty-state" colspan="6">Ainda não há eventos. Executa a simulação para gerar
                                registos de demonstração.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
