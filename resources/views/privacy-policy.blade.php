<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-12 text-stone-200 font-sans">
        <!-- Botón de regreso -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                <span>Volver al inicio</span>
            </a>
        </div>

        <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 sm:p-10 shadow-xl space-y-8">
            <!-- Encabezado -->
            <div class="border-b border-zinc-800 pb-6">
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-100 tracking-wide mb-2">Aviso de Privacidad</h1>
                <p class="text-xs text-stone-400 uppercase tracking-wider">Última actualización: {{ date('d/m/Y') }}</p>
            </div>

            <!-- Contenido -->
            <div class="space-y-6 text-sm text-stone-300 leading-relaxed font-sans">
                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">1. Identidad y Domicilio del Responsable</h2>
                    <p>
                        <strong>FIND ME</strong> es responsable del tratamiento y protección de sus datos personales. Este Aviso de Privacidad regula la forma en que recolectamos, utilizamos, almacenamos y protegemos la información proporcionada por los usuarios en nuestra plataforma de objetos perdidos y encontrados.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">2. Datos Personales Recabados</h2>
                    <p class="mb-2">Para el correcto funcionamiento del servicio, recopilamos los siguientes datos:</p>
                    <ul class="list-disc list-inside space-y-1 text-stone-400 pl-2">
                        <li>Nombre de usuario y dirección de correo electrónico al registrarse.</li>
                        <li>Fotografías, ubicaciones e información general de los objetos reportados (perdidos o encontrados).</li>
                        <li>Mensajes e interacciones enviados a través del chat privado dentro de la plataforma.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">3. Finalidad del Tratamiento de Datos</h2>
                    <p class="mb-2">Los datos personales recabados serán utilizados para las siguientes finalidades principales:</p>
                    <ul class="list-disc list-inside space-y-1 text-stone-400 pl-2">
                        <li>Permitir la publicación y búsqueda de objetos perdidos y encontrados.</li>
                        <li>Facilitar la comunicación directa y segura entre usuarios a través de la mensajería interna.</li>
                        <li>Garantizar la seguridad de los datos de acceso mediante autenticación y encriptación.</li>
                        <li>Prevenir usos indebidos, fraudes o comportamientos inapropiados en la plataforma.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">4. Protección y Seguridad de los Datos</h2>
                    <p>
                        Implementamos medidas de seguridad técnicas, administrativas y físicas para proteger sus datos personales contra daño, pérdida, alteración, destrucción o el uso, acceso o tratamiento no autorizado. Las contraseñas de las cuentas están encriptadas mediante algoritmos de seguridad estándar.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">5. Derechos ARCO (Acceso, Rectificación, Cancelación y Oposición)</h2>
                    <p>
                        Como usuario, usted tiene derecho a acceder a sus datos personales en nuestro poder, rectificarlos en caso de ser inexactos o incompletos, cancelarlos cuando considere que no se requieren para las finalidades señaladas, u oponerse al tratamiento de los mismos. Puede editar o eliminar sus publicaciones en cualquier momento desde su panel de control.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">6. Transferencia de Datos</h2>
                    <p>
                        <strong>FIND ME</strong> no vende, alquila ni comparte sus datos personales con terceros no autorizados bajo ningún concepto, salvo por requerimientos judiciales o legales pertinentes.
                    </p>
                </section>

                <section>
                    <h2 class="text-base font-semibold text-stone-100 uppercase tracking-wider mb-2">7. Modificaciones al Aviso de Privacidad</h2>
                    <p>
                        Nos reservamos el derecho de efectuar en cualquier momento modificaciones o actualizaciones al presente Aviso de Privacidad. Cualquier cambio será publicado dentro de esta misma sección.
                    </p>
                </section>
            </div>

            <div class="border-t border-zinc-800 pt-6 flex justify-between items-center text-xs text-stone-500">
                <span>FIND ME © {{ date('Y') }} — Todos los derechos reservados</span>
                <a href="{{ route('dashboard') }}" wire:navigate class="text-stone-300 hover:text-white transition-colors">Volver al Inicio</a>
            </div>
        </div>
    </div>
</x-app-layout>