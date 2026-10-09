<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionCatalogSeeder extends Seeder
{
    /**
     * Catálogo canónico de permisos de AutoMarket Pro.
     *
     * Este seeder es idempotente: no elimina permisos existentes ni asigna
     * permisos a roles. La asignación se realiza posteriormente desde RBAC.
     */
    public function run(): void
    {
        $catalog = [
            'usuarios' => [
                'ver' => 'Consultar usuarios.',
                'crear' => 'Crear usuarios.',
                'editar' => 'Editar datos de usuarios.',
                'eliminar' => 'Eliminar usuarios.',
                'cambiar_estado' => 'Activar, desactivar o cambiar el estado de usuarios.',
                'cambiar_rol' => 'Asignar o cambiar el rol de un usuario.',
                'restablecer_password' => 'Restablecer la contraseña de un usuario.',
            ],
            'clientes' => [
                'ver' => 'Consultar clientes.',
                'crear' => 'Crear clientes.',
                'editar' => 'Editar clientes.',
                'eliminar' => 'Eliminar clientes.',
                'cambiar_estado' => 'Cambiar el estado de clientes.',
            ],
            'vendedores' => [
                'ver' => 'Consultar vendedores.',
                'crear' => 'Crear vendedores.',
                'editar' => 'Editar vendedores.',
                'eliminar' => 'Eliminar vendedores.',
                'cambiar_estado' => 'Cambiar el estado de vendedores.',
            ],
            'concesionarios' => [
                'ver' => 'Consultar concesionarios.',
                'crear' => 'Crear concesionarios.',
                'editar' => 'Editar concesionarios.',
                'eliminar' => 'Eliminar concesionarios.',
                'cambiar_estado' => 'Cambiar el estado de concesionarios.',
            ],
            'dealer_branches' => [
                'ver' => 'Consultar sedes de concesionarios.',
                'crear' => 'Crear sedes.',
                'editar' => 'Editar sedes.',
                'eliminar' => 'Eliminar sedes.',
                'cambiar_estado' => 'Cambiar el estado de sedes.',
            ],
            'vehiculos' => [
                'ver' => 'Consultar vehículos.',
                'crear' => 'Crear vehículos.',
                'editar' => 'Editar vehículos.',
                'eliminar' => 'Eliminar vehículos.',
                'cambiar_propietario' => 'Cambiar el propietario del vehículo.',
                'gestionar_media' => 'Gestionar fotografías y archivos del vehículo.',
            ],
            'publicaciones' => [
                'ver' => 'Consultar publicaciones.',
                'crear' => 'Crear publicaciones.',
                'editar' => 'Editar publicaciones.',
                'eliminar' => 'Eliminar publicaciones.',
                'publicar' => 'Publicar avisos.',
                'pausar' => 'Pausar publicaciones.',
                'reanudar' => 'Reanudar publicaciones.',
                'rechazar' => 'Rechazar publicaciones.',
                'suspender' => 'Suspender publicaciones.',
                'marcar_vendido' => 'Marcar publicaciones como vendidas.',
                'destacar' => 'Destacar publicaciones.',
            ],
            'moderacion' => [
                'ver' => 'Consultar casos de moderación.',
                'asignar' => 'Asignar casos de moderación.',
                'revisar' => 'Revisar casos.',
                'aprobar' => 'Aprobar publicaciones o casos.',
                'rechazar' => 'Rechazar publicaciones o casos.',
                'suspender' => 'Suspender publicaciones o casos.',
                'ver_historial' => 'Consultar historial de moderación.',
            ],
            'planes' => [
                'ver' => 'Consultar planes.',
                'crear' => 'Crear planes.',
                'editar' => 'Editar planes.',
                'eliminar' => 'Eliminar planes.',
                'activar' => 'Activar planes.',
                'desactivar' => 'Desactivar planes.',
            ],
            'pagos' => [
                'ver' => 'Consultar pagos.',
                'consultar' => 'Consultar detalle y estado de pagos.',
                'aprobar' => 'Aprobar pagos.',
                'rechazar' => 'Rechazar pagos.',
                'cancelar' => 'Cancelar pagos.',
                'reembolsar' => 'Procesar reembolsos.',
                'reconciliar' => 'Ejecutar o consultar conciliación de pagos.',
            ],
            'facturas' => [
                'ver' => 'Consultar facturas.',
                'crear' => 'Crear facturas.',
                'consultar' => 'Consultar detalle de facturas.',
                'anular' => 'Anular facturas.',
                'descargar' => 'Descargar facturas.',
            ],
            'leads' => [
                'ver' => 'Consultar leads.',
                'crear' => 'Crear leads.',
                'editar' => 'Editar leads.',
                'eliminar' => 'Eliminar leads.',
                'asignar' => 'Asignar leads.',
                'cambiar_estado' => 'Cambiar el estado del lead.',
            ],
            'lead_interactions' => [
                'ver' => 'Consultar interacciones de leads.',
                'crear' => 'Registrar interacciones de leads.',
            ],
            'test_drive' => [
                'ver' => 'Consultar citas de prueba de conducción.',
                'crear' => 'Crear citas.',
                'editar' => 'Editar citas.',
                'cancelar' => 'Cancelar citas.',
                'confirmar' => 'Confirmar citas.',
                'completar' => 'Marcar citas como completadas.',
                'marcar_no_show' => 'Marcar citas como no show.',
            ],
            'mensajes' => [
                'ver' => 'Consultar conversaciones y mensajes.',
                'enviar' => 'Enviar mensajes.',
                'marcar_leido' => 'Marcar mensajes como leídos.',
                'archivar' => 'Archivar conversaciones.',
                'bloquear' => 'Bloquear conversaciones.',
            ],
            'publicidad' => [
                'ver' => 'Consultar elementos de publicidad.',
                'crear' => 'Crear elementos de publicidad.',
                'editar' => 'Editar elementos de publicidad.',
                'eliminar' => 'Eliminar elementos de publicidad.',
                'activar' => 'Activar elementos de publicidad.',
                'desactivar' => 'Desactivar elementos de publicidad.',
            ],
            'reportes' => [
                'ver' => 'Consultar reportes.',
                'exportar' => 'Exportar reportes.',
            ],
            'configuracion' => [
                'ver' => 'Consultar configuración.',
                'editar' => 'Modificar configuración.',
            ],
            'roles' => [
                'ver' => 'Consultar roles.',
                'crear' => 'Crear roles.',
                'editar' => 'Editar roles.',
                'eliminar' => 'Eliminar roles.',
                'asignar_permisos' => 'Asignar permisos a roles.',
            ],
            'permisos' => [
                'ver' => 'Consultar permisos.',
                'crear' => 'Crear permisos.',
                'editar' => 'Editar permisos.',
                'eliminar' => 'Eliminar permisos.',
                'asignar_rol' => 'Asignar permisos a roles o usuarios.',
            ],
            'auditoria' => [
                'ver' => 'Consultar registros de auditoría.',
                'exportar' => 'Exportar registros de auditoría.',
            ],
            'logs' => [
                'ver' => 'Consultar logs del sistema.',
                'exportar' => 'Exportar logs del sistema.',
            ],
        ];

        foreach ($catalog as $module => $actions) {
            foreach ($actions as $action => $description) {
                Permission::query()->updateOrCreate(
                    [
                        'name' => $module . '.' . $action,
                        'guard_name' => 'web',
                    ],
                    [
                        'description' => $description,
                    ],
                );
            }
        }
    }
}
