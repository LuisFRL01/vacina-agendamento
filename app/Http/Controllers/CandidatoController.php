<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidato;
use App\Models\Lote;
use App\Models\PostoVacinacao;
use Carbon\Carbon;

class CandidatoController extends Controller
{
    public function show(Request $request) {
        $candidatos = null;

        if($request->filtro == null || $request->filtro == 1) {
            $candidatos = Candidato::where('aprovacao', Candidato::APROVACAO_ENUM[0])->get();
        } else if ($request->filtro == 2) {
            $candidatos = Candidato::where('aprovacao', Candidato::APROVACAO_ENUM[1])->get();
        } else if ($request->filtro == 3) {
            $candidatos = Candidato::where('aprovacao', Candidato::APROVACAO_ENUM[2])->get();
        }
        
        return view('dashboard')->with(['candidatos' => $candidatos]);
    }
  
    public function solicitar() {

        // TODO: pegar só os postos com vacinas disponiveis
        $postos_com_vacina = PostoVacinacao::all();

        return view("form_solicitacao")->with([
            "sexos" => Candidato::SEXO_ENUM,
            "postos" => $postos_com_vacina
        ]);
    }

    public function enviar_solicitacao(Request $request) {

        $request->validate([
            "nome_completo"         => "required",
            "data_de_nascimento"    => "required|date",
            "cpf"                   => "required",
            "número_cartão_sus"     => "required",
            "sexo"                  => "required",
            "nome_da_mãe"           => "required",
            "foto_frente_rg"        => "required|mimes:jpeg,jpg,jpe,png,bmp",
            "foto_tras_rg"          => "required|mimes:jpeg,jpg,jpe,png,bmp",
            "telefone"              => "required",
            "whatsapp"              => "nullable",
            "email"                 => "nullable",
            "cep"                   => "nullable",
            "cidade"                => "required",
            "bairro"                => "required",
            "rua"                   => "required",
            "número_residencial"    => "required",
            "complemento_endereco"  => "nullable",
            "posto_vacinacao"       => "required",
            "dia_vacinacao"         => "required",
            "horario_vacinacao"     => "required",
        ]);

        $dados = $request->all();
        
        $candidato = new Candidato;
        $candidato->nome_completo           = $request->nome_completo;
        $candidato->data_de_nascimento      = $request->data_de_nascimento;
        $candidato->cpf                     = $request->cpf;
        $candidato->numero_cartao_sus       = $request->input("número_cartão_sus");
        $candidato->sexo                    = $request->sexo;
        $candidato->nome_da_mae             = $request->input("nome_da_mãe");
        $candidato->telefone                = $request->telefone;
        $candidato->whatsapp                = $request->whatsapp;
        $candidato->email                   = $request->email;
        $candidato->cep                     = $request->cep;
        $candidato->cidade                  = $request->cidade;
        $candidato->bairro                  = $request->bairro;
        $candidato->logradouro              = $request->rua;
        $candidato->numero_residencia       = $request->input("número_residencial");
        $candidato->complemento_endereco    = $request->nome_completo;


        
        $dia_vacinacao = $request->dia_vacinacao;
        $horario_vacinacao = $request->horario_vacinacao;
        $id_posto = $request->posto_vacinacao;
        $datetime_chegada = Carbon::createFromFormat("d/m/Y H:i" , $dia_vacinacao . " " . $horario_vacinacao);
        $datetime_saida = $datetime_chegada->copy()->addMinutes(10);

        $candidatos_no_mesmo_horario_no_mesmo_lugar = Candidato::where("chegada", "=", $datetime_chegada)->where("posto_vacinacao_ìd", $id_posto)->get();

        if($candidatos_no_mesmo_horario_no_mesmo_lugar->count() > 0) {
            return redirect()->back()->withErrors([
                "posto_vacinacao" => "Alguém conseguiu preencher o formulário mais rápido que você, escolha outro horario por favor"
            ])->withInput();
        }


        // A logica da escolha do lote é a seguinte
        // Um lote é dividido igualmente para todos os postos
        // Então doses/num_postos é o maximo que uma pessoa pode ser alocada para o lote naquele posto
        // Obviamente isso é falho, como quando se adicionar um posto após as vacinas terem começado
        // TODO: melhorar alocação dos lotes

        $lotes = Lote::all();
        $num_postos = PostoVacinacao::count();
        $id_lote = 0;

        foreach($lotes as $lote) {
            if(Candidato::where("lote_id", $lote->id)->count() < ($lote->qtdVacina/ $num_postos)) {
                $id_lote = $lote->id;
                break;
            }
        }

         if($id_lote == 0) {
            return redirect()->back()->withErrors([
                "posto_vacinacao" => "Não existem vacinas dispoiveis nesse posto..."
            ])->withInput();
        }


        $candidato->chegada                 = $datetime_chegada;
        $candidato->saida                   = $datetime_saida;
        $candidato->lote_id                 = $id_lote;
        $candidato->posto_vacinacao_ìd      = $id_posto;

        $candidato->paciente_acamado = isset($dados["paciente_acamado"]);

        if(isset($dados["paciente_agente_de_saude"])) {
            $candidato->paciente_agente_de_saude = true;
            $candidato->unidade_caso_agente_de_saude = $dados["unidade_caso_agente_de_saude"];
        } else {
            $candidato->paciente_agente_de_saude = false;
            $candidato->unidade_caso_agente_de_saude = "Não informado";
        }

        $candidato->foto_frente_rg = $request->file('foto_frente_rg')->store("public");
        $candidato->foto_tras_rg = $request->file('foto_tras_rg')->store("public");
        $candidato->save();
        
        return redirect()->back()->with('status', 'Cadastrado com sucesso');

    }

    public function uploadFile($request, $input, $nome){
    	if($request->hasFile($input)){
    		$path = $request->photo->storeAs('images', $nome, 'public');

    		return $path;
    	}
    	return null;
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'confirmacao' => 'required'
        ]);

        $candidato = Candidato::find($id);

        if ($request->confirmacao == 1) {
            $candidato->candidato_aprovado = true;
        } else if ($request->confirmacao == 0) {
            $candidato->candidato_aprovado = false;
        }

        $candidato->update();
        
        return redirect()->back()->with(['mensagem' => 'Resposta salva com sucesso!']);
    }
}
