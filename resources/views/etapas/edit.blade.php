<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar') }} {{$publico->texto}}
        </h2>
    </x-slot>
    <div style="margin-top: 30px;">
        <form id="editar_etapa_{{$publico->id}}" action="{{route('etapas.update', ['id' => $publico->id])}}" method="post">
            @csrf
            <div class="container" style="margin-bottom: 35px;">
                @if(session('error'))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger" role="alert">
                                <p>{{session('error')}}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="mb-2 mt-4">
                            <label for="isDias" >Marque para caso seja número de dias e desmarque caso seja por data:</label>
                            <input id="isDias" type="checkbox" name="isDias" @if(old('isDias') || (old('isDias') == null && $publico->isDias)) checked @endif>
                        </div>
                        
                    </div>
                    <div class="col-md-6 " id="numero_dias" @if (!$publico->isDias) style="display:none" @endif>
                        <label for="numero_dias" >Número de dias entre a D2 e D3</label>
                        <input  class="form-control" type="number"  name="numero_dias" value="{{$publico->numero_dias}}" >
                        
                    </div>
                    <div class="col-md-6" id="intervalo_reforco" @if ($publico->isDias) style="display:none" @endif>
                        <label for="intervalo_reforco" >Definir data limite para segunda dose</label>
                        <input  class="form-control" type="date"  name="intervalo_reforco" value="{{date('Y-m-d',strtotime($publico->intervalo_reforco) )}}" >
                        
                    </div>
                    <div class="col-md-12 mt-2 mb-5">
                        <div class="mb-2 mt-4">
                            <label for="dose_tres" >Exibir público no agendamento na dose de Reforço</label>
                            <input id="dose_tres" type="checkbox" name="dose_tres" @if(old('dose_tres') || (old('dose_tres') == null && $publico->dose_tres)) checked @endif>
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        <label for="tipo">Classficação do público</label>
                        <select name="tipo" id="tipo" class="form-control" onchange="selecionarDiv(this)">
                            <option value="" selected disabled>-- selecione a classificação do público --</option>
                            @if (old('tipo') != null)
                                <option value="{{$tipos[0]}}" @if(old('tipo') == $tipos[0]) selected @endif>Por idade</option>
                                <option value="{{$tipos[1]}}" @if(old('tipo') == $tipos[1]) selected @endif>Texto livre</option>
                                <option value="{{$tipos[2]}}" @if(old('tipo') == $tipos[2]) selected @endif>Texto livre com campo extra selecionável</option>
                            @else 
                                <option value="{{$tipos[0]}}" @if($publico->tipo == $tipos[0]) selected @endif>Por idade</option>
                                <option value="{{$tipos[1]}}" @if($publico->tipo == $tipos[1]) selected @endif>Texto livre</option>
                                <option value="{{$tipos[2]}}" @if($publico->tipo == $tipos[2]) selected @endif>Texto livre com campo extra selecionável</option>
                            @endif
                        </select>

                        @error('tipo')
                            <div id="tipo" class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="texto_do_agendamento">Texto exibido no agendamento</label>
                        <input type="text" id="texto_do_agendamento" name="texto_do_agendamento" class="form-control" value="@if(old('texto_do_agendamento')!=null){{old('texto_do_agendamento')}}@else{{$publico->texto}}@endif">

                        @error('texto_do_agendamento')
                            <div id="texto_do_agendamento" class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                        @enderror
                        <input id="exibir_no_form" type="checkbox" name="exibir_no_form" @if(old('exibir_no_form') || (old('exibir_no_form') == null && $publico->exibir_no_form)) checked @endif>
                        <label for="exibir_no_form" >Exibir público no agendamento</label>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="texto_da_home">Texto exibido na home</label>
                        <input id="texto_da_home" type="text" class="form-control @error('texto_da_home') is-invalid @enderror" name="texto_da_home" value="@if(old('texto_da_home')!=null){{old('texto_da_home')}}@else{{$publico->texto_home}}@endif">

                        @error('texto_da_home')
                            <div id="validationServer05Feedback" class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                        @enderror
                        <input id="exibir_na_home" type="checkbox" name="exibir_na_home" @if(old('exibir_na_home') || (old('exibir_na_home') == null && $publico->exibir_na_home)) checked @endif>
                        <label for="exibir_na_home" >Exibir público na home</label>
                    </div>
                </div>
                <br>
                <div id="divIdade" @if(old('tipo') == $tipos[0] || old('tipo') == $tipos[2] || (old('tipo') == null && $publico->tipo == $tipos[0] || $publico->tipo == $tipos[2])) style="display: block;" @else style="display: none;" @endif>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="inicio_faixa_etaria">Inicio da faixa etaria</label>
                            <input id="inicio_faixa_etaria" class="form-control @error('inicio_faixa_etária') is-invalid @enderror" type="number" name="inicio_faixa_etária" placeholder="80" value="@if(old('inicio_faixa_etária') != null){{old('inicio_faixa_etária')}}@else{{$publico->inicio_intervalo}}@endif" min="0" max="1000">
                        
                            @error('inicio_faixa_etária')
                                <div id="validationServer05Feedback" class="invalid-feedback">
                                    <strong>{{$message}}</strong>
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="fim_faixa_etaria">Fim da faixa etaria</label>
                            <input id="fim_faixa_etaria" class="form-control @error('fim_faixa_etária') is-invalid @enderror" type="number" name="fim_faixa_etária" placeholder="85" value="@if(old('fim_faixa_etária') != null){{old('fim_faixa_etária')}}@else{{$publico->fim_intervalo}}@endif" min="0" max="1000">
                            
                            @error('fim_faixa_etária')
                                <div id="validationServer05Feedback" class="invalid-feedback">
                                    <strong>{{$message}}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <br>
                </div>
                <div id="divOpcoes" style="@if(old('tipo') == $tipos[2] || (old('tipo') == null && $publico->tipo == $tipos[2])) display: block; @else display: none; @endif 
                                        border: 1px solid rgb(196, 196, 196);
                                        padding: 15px;
                                        border-radius: 10px;">
                    <label>Opções do campo selecionável</label>
                    <div id="divTodasOpcoes" class="row">
                        @if (old('opcoes') != null) 
                            @foreach (old('opcoes') as $i => $textoOpcao)
                                <div class="col-md-5" style="border: 1px solid rgb(196, 196, 196);
                                            padding: 15px;
                                            border-radius: 10px;
                                            margin: 15px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label>Opção</label>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input type="hidden" name="op_ids[]" value="{{'op_ids.'.$i}}">
                                                    <input type="text" name="opcoes[]" class="form-control @error('opcoes.'.$i) is-invalid @enderror" placeholder="Digite a opção selecionável" value="{{$textoOpcao}}">
                                                    @error('opcoes.'.$i)
                                                        <div id="validationServer05Feedback" class="invalid-feedback">
                                                            <strong>{{$message}}</strong>
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <a class="btn btn-danger" onclick="excluirOpcao(this)" style="cursor: pointer; color: white;">Excluir</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif(old('opcoes') == null)
                            @if ($publico->opcoes)
                                @foreach ($publico->opcoes as $op)
                                    <div class="col-md-5" style="border: 1px solid rgb(196, 196, 196);
                                                padding: 15px;
                                                border-radius: 10px;
                                                margin: 15px;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Opção</label>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <input type="hidden" name="op_ids[]" value="{{$op->id}}">
                                                        <input type="text" name="opcoes[]" class="form-control" placeholder="Digite a opção selecionável" value="{{$op->opcao}}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <a class="btn btn-danger" onclick="excluirOpcao(this)" style="cursor: pointer; color: white;">Excluir</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                    <br>
                    <div class="row" style="text-align: right">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info" onclick="adicionarOpcao()">Adicionar opção</button>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <label for="pri_dose">Total de pessoas vacinadas na 1ª dose</label>
                        <input id="pri_dose" class="form-control @error('primeria_dose') is-invalid @enderror" type="number" name="primeria_dose" placeholder="0" value="@if(old('primeria_dose') != null){{old('primeria_dose')}}@else{{$publico->total_pessoas_vacinadas_pri_dose}}@endif" min="0" max="1000000000000">

                        @error('primeria_dose')
                            <div id="validationServer05Feedback" class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="seg_dose">Total de pessoas vacinadas na 2ª dose</label>
                        <input id="seg_dose" class="form-control @error('segunda_dose') is-invalid @enderror" type="number" name="segunda_dose" placeholder="0" value="@if(old('segunda_dose')!=null){{old('segunda_dose')}}@else{{$publico->total_pessoas_vacinadas_seg_dose}}@endif" min="0" max="1000000000000">

                        @error('segunda_dose')
                            <div id="validationServer05Feedback" class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <input id="atual" type="checkbox" name="atual" @if(old('atual') || (old('atual') == null && $publico->atual)) checked @endif>
                        <label for="atual">A vacinação deste público esta ocorrendo atualmente.</label>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Vincular público a pontos</h5>
                    </div>
                </div>
                <br>
                <div class="row">
                    @error('pontos')
                        <div class="col-md-12">
                            <div id="validationServer05Feedback" class="invalid-feedback">
                                <strong>{{$message}}</strong>
                            </div>
                        </div>
                    @enderror
                    @foreach ($pontos as $ponto)
                        <div class="col-md-4">
                            <input type="checkbox" name="pontos[]" value="{{$ponto->id}}" @if((old('pontos') != null && in_array($ponto->id, old('pontos'))) || (old('pontos') == null && $publico->pontos != null && $publico->pontos->contains($ponto))) checked @endif>
                            <label for="">{{$ponto->nome}}</label>
                        </div>
                    @endforeach
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Outras informações</h5>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <input type="checkbox" name="outras_informações" onclick="exibirOutrasInfo(this)" @if(old('outras_informações') != null || (old('outras_informações') == null && $publico->outrasInfo != null && count($publico->outrasInfo) > 0)) checked @endif>
                        <label for="">Adicionar outras informações ao público</label>
                    </div>
                    <div class="col-md-6">
                        <input type="checkbox" name="outras_informações_obrigatorias" @if(old('outras_informações_obrigatorias') != null || (old('outras_informações_obrigatorias') == null && $publico->outras_opcoes_obrigatorio != null && $publico->outras_opcoes_obrigatorio)) checked @endif>
                        <label for="">Deixar outras informações obrigatórias</label>
                    </div>
                </div>
                <br>
                <div id="divOutrasInfo" style="@if(old('outras_informações') != null || (old('outras_informações') == null && $publico->outrasInfo != null && count($publico->outrasInfo) > 0)) display: block; @else display: none; @endif">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Texto das outras informações</label>
                            <textarea name="texto_das_outras_informações" class="form-control" id="texto_das_outras_informações" cols="30" rows="5" placeholder="Insira aqui o texto informativo">@if(old('texto_das_outras_informações') != null){{old('texto_das_outras_informações')}}@else{{$publico->texto_outras_informacoes}}@endif</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div  style="border: 1px solid rgb(196, 196, 196);
                                 padding: 15px;
                                 margin-top: 15px;
                                 margin-bottom: 15px;
                                 border-radius: 10px;">
                                <label>Opções de outras informações</label>
                                <div id="divTodasOutrasInfo" class="row">
                                    @if (old('outrasInfo') != null) 
                                        @foreach (old('outrasInfo') as $i => $textoOutraInfo)
                                            <div class="col-md-5" style="border: 1px solid rgb(196, 196, 196);
                                                        padding: 15px;
                                                        border-radius: 10px;
                                                        margin: 15px;">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>Opção</label>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input type="hidden" name="outrasInfo_id[]" value="{{old('outrasInfo_id.'.$i)}}">
                                                                <input type="text" name="outrasInfo[]" class="form-control @error('outrasInfo.'.$i) is-invalid @enderror" placeholder="Digite o texto da outra informação" value="{{$textoOutraInfo}}">
                                                                @error('outrasInfo.'.$i)
                                                                    <div id="validationServer05Feedback" class="invalid-feedback" style="text-align: justify;">
                                                                        <strong>{{$message}}</strong>
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-3">
                                                                <a class="btn btn-danger" onclick="excluirOpcao(this)"  style="cursor: pointer; color: white;">Excluir</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                    @else 
                                        @foreach($publico->outrasInfo as $outra) 
                                            <div class="col-md-5" style="border: 1px solid rgb(196, 196, 196);
                                                        padding: 15px;
                                                        border-radius: 10px;
                                                        margin: 15px;">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>Opção</label>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <input type="hidden" name="outrasInfo_id[]" value="{{$outra->id}}">
                                                                <input type="text" name="outrasInfo[]" class="form-control" placeholder="Digite o texto da outra informação" value="{{$outra->campo}}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <a class="btn btn-danger" onclick="excluirOpcao(this)"  style="cursor: pointer; color: white;">Excluir</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @error('outrasInfo')
                                        <div class="col-md-11 alert alert-danger" style="border: 1px solid rgb(196, 196, 196);
                                                            border-radius: 10px;
                                                            margin: 15px;">
                                                {{$message}}
                                        </div>
                                    @enderror
                                </div>
                                
                                <br>
                                <div class="row" style="text-align: right">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-info" onclick="adicionarOutraInfo()">Adicionar opção</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6" style="text-align: right;">
                        <a href="{{route('etapas.index')}}" class="btn btn-secondary" style="width: 100%; padding-top: 20px; padding-bottom: 20px; cursor:pointer; color:white;">Voltar</a>
                    </div>
                    <div class="col-md-6" style="text-align: right;">
                        <button type="submit" class="btn btn-success" style="width: 100%; padding-top: 20px; padding-bottom: 20px;">Salvar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function(){

            $("#isDias").click(function(){
                $("#numero_dias").toggle();
                $("#intervalo_reforco").toggle();
            });

        }); 
        

        function adicionarOpcao() {
            html = `<div class="col-md-5" style="border: 1px solid rgb(196, 196, 196);
                                    padding: 15px;
                                    border-radius: 10px;
                                    margin: 15px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Opção</label>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <input type="hidden" name="op_ids[]" value="0">
                                            <input type="text" name="opcoes[]" class="form-control" placeholder="Digite a opção selecionável">
                                        </div>
                                        <div class="col-md-3">
                                            <a class="btn btn-danger" onclick="excluirOpcao(this)"  style="cursor: pointer; color: white;">Excluir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`
            $('#divTodasOpcoes').append(html);
        }

        function excluirOpcao(button) {
            button.parentElement.parentElement.parentElement.parentElement.parentElement.remove();
        }

        function selecionarDiv(select) {
            
            valor = select.children[select.selectedIndex].textContent;
            if (valor == "Por idade") {
                document.getElementById('divIdade').style.display = "block";
                document.getElementById('divOpcoes').style.display = "none";
                excluirOpcoes();
            } else if (valor == "Texto livre") {
                /* alert(valor); */
                document.getElementById('divIdade').style.display = "none";
                document.getElementById('divOpcoes').style.display = "none";
                document.getElementById('inicio_faixa_etaria').value = "";
                document.getElementById('fim_faixa_etaria').value = "";
                excluirOpcoes();
            } else if (valor == "Texto livre com campo extra selecionável") {
                /* alert(valor); */
                document.getElementById('divIdade').style.display = "block";
                document.getElementById('divOpcoes').style.display = "block";
                document.getElementById('inicio_faixa_etaria').value = "";
                document.getElementById('fim_faixa_etaria').value = "";
                adicionarOpcao();
            }
        }

        function excluirOpcoes() {
            
            var todasOpcoes = document.getElementById('divTodasOpcoes');
            
            while (todasOpcoes.firstChild) {
                todasOpcoes.removeChild(todasOpcoes.lastChild);
            }
        }

        function exibirOutrasInfo(input) {
            if(input.checked) {
                document.getElementById('divOutrasInfo').style.display = "block";
                adicionarOutraInfo();
            } else {
                document.getElementById('divOutrasInfo').style.display = "none";
                excluirOutrasInfo();
            }
        }

        function adicionarOutraInfo() {
            html = `<div class="col-md-5" style="border: 1px solid rgb(196, 196, 196);
                                    padding: 15px;
                                    border-radius: 10px;
                                    margin: 15px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Opção</label>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <input type="hidden" name="outrasInfo_id[]" value="0">
                                            <input type="text" name="outrasInfo[]" class="form-control" placeholder="Digite o texto da outra informação">
                                        </div>
                                        <div class="col-md-3">
                                            <a class="btn btn-danger" onclick="excluirOpcao(this)"  style="cursor: pointer; color: white;">Excluir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`
            $('#divTodasOutrasInfo').append(html);
        }
        
        function excluirOutrasInfo() {
            var todasOutrasInfos = document.getElementById('divTodasOutrasInfo');
            while (todasOutrasInfos.firstChild) {
                todasOutrasInfos.removeChild(todasOutrasInfos.lastChild);
            }
        }
    </script>
</x-app-layout>
