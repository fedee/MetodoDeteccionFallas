##Implementación del método CAFAP (Detección de Fallas en Piezas Mecánicas)

Hola gente, todo aquel que lo desee puede hacer uso del código de este repositorio.

Se trata de un proyecto Universitario de investigación realizado en conjunto con alumnos de Ingeniería Mecánica de la Universidad Católica de Córdoba, donde el tópico de dicha investigación es la detección de posibles causas de fallas en piezas mecánicas. 

Estoy desarrollando una web que implementa este método, utilizando el Framework de PHP  **CodeIgniter**, basado en el Modelo-Vista-Controlador, el cual me ha facilitado bastante mi trabajo a través de sus librerías y helpers.

Espero que a más de uno le sirva y pueda reutilizar algunas features que estoy codificando y voy a seguir implementando posteriormente hasta finalizar el trabajo. 

Antes de empezar, cabe aclarar que el Framework es muy simple de implementar. Particularmente estoy usando como servidor local Apache en XAMPP; por lo que hay que descargar XAMPP, descargar este proyecto, ir a la carpeta htdocs en el directorio de XAMPP y crear una nueva de título "MiProyecto" por ejemplo, copiar todo el código de este repositorio allí y listo! 

Lo que hay codificado por ahora:

1. Login de usuarios (3 tipos diferentes: Administrador, Especialista y Usuario Común).
2. Al usuario Especialista, lo debe activar el Administrador. Este usuario es el que se encarga de analizar el caso cargado por el Usuario Común.
3. El Administrador asigna casos pendientes a algún Especialista.
4. El Usuario Común se registra, y puede cargar su caso (que consta una serie de pasos que lo van guiando en cuanto a la info que debe ir cargando). 
5. Validaciones de formularios utilizando la librería "form_validation", la cual corrobora entre otras cosas que el email ingresado sea válido, control de campos vacíos, evitar inyecciones XSS, mínimo de caracteres, máximo de caracteres, etc.
6. Uso del helper "url" para generación de urls más "amigables", y funciones útiles para el tratamiento de las mismas.
7. Uso de las librerías "database" para un manejo fácil de la base de datos y ejecutar las consultas, y "session" para el manejo de sesiones. Crearlas, obtener datos y actualizarlas.
8. Cada tipo de usuario tiene su respectivo panel. 
9. Uso de la librería "upload" para el tratamiento con imágenes. Guardado de imágenes en carpetas dentro del proyecto y la ruta a las mismas para acceder en la base de datos, y creación de miniaturas (thumbnails). 
10. Reutilización de elementos Bootstrap, y fragmentos de panel de usuario y dashboard basado en el mismo (sb-admin). Uso de diversos componentes como iconos, font-awesome, panels, labels, alerts, buttons, forms, diversos componentes basandome en su sistema de grillas (grids).
11. Implementación de AJAX para llenar select en cascada, completar una tabla según datos seleccionados de uno de los select, y traer partes de formulario HTML.

Detalles a mejorar en algún momento:
- Que los casos por asignar se paginen, y haya un buscador tanto para casos como para especialistas, esto en la parte del admin en "Asignar Casos".
- Validación por email del correo de tanto especialistas como usuarios comunes.
- Permisos para ver ciertas paginas.
- Que el caso quede abierto hasta que el usuario común decida, que no quede finalizado cuando completa el caso.
- El sistema de mensajería, donde el especialista pueda solicitar cambios en determinada respuesta y e usuario común también pueda preguntar dudas en determinada pregunta.
- Enumerar las preguntas.
- Crear vistas para que el usuario común pueda editar su caso, ya ingresado anteriormente.

Podría tener (si llego con el tiempo):
- Un archivo de configuración.
- Implementación del módulo FEA de análisis de elementos finitos para piezas simples.

Lo que viene en el próximo Sprint:
- Actualmente me encuentro desarrollando la paginación, utilizando la librería "pagination" del Framework que facilita el trabajo. 
- El objetivo de la paginación, será implementar un sistema de mensajería entre usuarios-especialistas y especialistas-admin. 
- Completar el método científico en el que está basado en el trabajo (hay codificados sólo 2 pasos de aproximadamente 15).

Lo que resta del Backlog:
- Una vez completado el método, para el Usuario Común, puede cargar un caso completo (asignado con un especialista), y el especialista en su panel podrá acceder a el, y todos los casos que ha atendido. 
- Habrá un módulo de estadísticas, actualmente se encuentra un ejemplo en el panel del especialista, utilizando Morris Charts (librería de gráficos interactivos). Analizaré si integrar algunos propios de Google.
- Que el sistema sugiera la causa más probable de la falla para el Usuario Común, basándose en estadística implementada en el controller "casos". 

Como se puede observar, el Backlog está escrito muy a grandes razgos, a medida que vaya avanzando iré actualizando este documento con las nuevas implementaciones. Espero que más de uno pueda reutilizar buena parte del código, donde se presentan muchas funcionalidades típicas. 

Saludos, **Fede Sarmiento**. 
