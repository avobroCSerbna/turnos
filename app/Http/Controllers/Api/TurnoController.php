<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PlanHorarioRequest;
use App\Http\Requests\ReservarTurnoPublicoRequest;
use App\Http\Resources\TurnoCalendarResource;
use App\Http\Resources\TurnoConfirmadoResource;
use App\Http\Resources\TurnoDisponibleResource;
use App\Http\Resources\TurnoSinConfirmarResource;
use App\Mail\ReservarTurno;
use App\Services\EspecialidadService;
use App\Services\PacienteService;
use App\Services\TurnoService;
use App\Services\MedicoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TurnoController extends Controller
{
    protected $medicoService;
    protected $turnoService;
    protected $pacienteService;

    public function __construct(TurnoService $turnoService,
                                MedicoService $medicoService,
                                PacienteService $pacienteService)
    {
        $this->medicoService = $medicoService;
        $this->turnoService = $turnoService;
        $this->pacienteService = $pacienteService;
    }

    /**
     * Devuelve una colección con todos los turno vigentes del medico autenticado
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getMisTurnosActuales()
    {
        $medico = Auth::user()->medico;
        return TurnoCalendarResource::collection(
            $this->turnoService->getMisTurnosActuales($medico)
        );
    }

    /**
     * Retorna al usuario recepcionista una vista con los turnos sin confirmar,
     * y así poder confirmarlos cuando los pacientes arriben.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTurnosSinConfirmar()
    {
        return TurnoSinConfirmarResource::collection(
            $turnos = \App\Turno::whereDate('fecha', '=', '2018-11-12')->get()
        );
        /*
        return TurnoSinConfirmarResource::collection(
            $turnos = $this->turnoService->getTurnosSinConfirmar()
        );
        */
    }

    /**
     * Retorna los turnos que están que ya han sido confirmados.
     * Lo cual significa que el médico ya puede atenderlos
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getMisTurnosConfirmados()
    {
        $medico = Auth::user()->medico;
        return TurnoConfirmadoResource::collection(
            $this->turnoService->getMisTurnosConfirmados($medico)
        );
    }

    /**
     * Devuelve una colección con todos los turno disponibles de un medico
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function buscarPorMedico($id)
    {
        $medico = $this->medicoService->find($id);
        return TurnoCalendarResource::collection(
            $this->turnoService->buscarPorMedico($medico)
        );
    }

    /**
     * Devuelve una colección con todos los turno disponibles de un medico
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function disponiblesPorMedico($id)
    {
        $medico = $this->medicoService->find($id);
        return TurnoDisponibleResource::collection(
            $this->turnoService->buscarPorMedico($medico)
        );
    }

    /**
     *  Devuelve una colección con todos los turno disponibles de una especialidad
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function buscarPorEspecialidad($id)
    {
        $espServ = new EspecialidadService();
        $especialidad = $espServ->find($id);
        return TurnoCalendarResource::collection(
            $espServ->turnosPorEspecialidad($especialidad)
        );
    }

    /**
     *  Devuelve una colección con todos los turno disponibles de una especialidad
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function disponiblesPorEspecialidad($id)
    {
        $espServ = new EspecialidadService();
        $especialidad = $espServ->find($id);
        return TurnoDisponibleResource::collection(
            $espServ->turnosPorEspecialidad($especialidad)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PlanHorarioRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function planificarHorarios(PlanHorarioRequest $request)
    {
        $input = $request->validated();
        $medico = Auth::user()->medico;
        $turnos = $this->turnoService->planificarHorarios($input, $medico);
        return TurnoCalendarResource::collection($turnos);
    }

    /**
     * Store a newly created resource in storage.
     * @param $turno
     * @param $paciente
     */
    public function reservarTurno($turno, $paciente)
    {
        $turno = $this->turnoService->find($turno);
        $paciente = $this->pacienteService->find($paciente);
        $this->turnoService->reservarTurno($turno, $paciente);
        response()->json(['success' => 'success'], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param ReservarTurnoPublicoRequest $request
     */
    public function reservarTurnoPublico(ReservarTurnoPublicoRequest $request)
    {
        $input = $request->validated();
        $turno = $this->turnoService->find(
            $input['turno']
        );
        $paciente = $this->pacienteService->find(
            $input['paciente']
        );
        //$this->turnoService->reservarTurno($turno, $paciente);
        Mail::to($paciente->email)->send(new ReservarTurno($turno, $paciente));
        response()->json(['success' => 'success'], 200);
    }

    /**
     * Confirma un turno minutos antes de que se realice la atencion del mismo
     * @param $turno
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmarTurno($turno)
    {
        $this->turnoService->confirmarTurno($turno);
        return parent::noContent();
    }

    /**
     * Cancela un turno, el cual estará disponible para ser reservado por otro pacieinte
     * @param $turno
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelarTurno($turno)
    {
        $this->turnoService->cancelarTurno($turno);
        return parent::noContent();
    }

    /**
     * Finaliza un turno, lo cual significa que fue atendido
     * @param $turno
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalizarTurno($turno)
    {
        $this->turnoService->finalizarTurno($turno);
        return parent::noContent();
    }
}
