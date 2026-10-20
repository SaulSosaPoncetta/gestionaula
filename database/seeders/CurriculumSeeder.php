<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;
use App\Models\EjeCurricular;
use App\Models\NucleoCurricular;
use App\Models\TemaCurricular;

/**
 * Seeder del Diseño Curricular — DGCyE Buenos Aires (Ley 13688)
 * Secundaria Común — 1°, 2° y 3° año
 * Materias: Matemática y Prácticas del Lenguaje
 *
 * Busca las materias globales (user_id IS NULL) por nombre + anio.
 * Si no encuentra con user_id=null, usa la primera que encuentre.
 */
class CurriculumSeeder extends Seeder
{
    // Mapa año numérico → string guardado en la columna `anio` de materias
    private array $anioMap = [
        1 => '1°',
        2 => '2°',
        3 => '3°',
    ];

    public function run(): void
    {
        $this->seedMatematica();
        $this->seedPracticasDelLenguaje();
    }

    // ─────────────────────────────────────────────────────────────────────
    // MATEMÁTICA
    // ─────────────────────────────────────────────────────────────────────
    private function seedMatematica(): void
    {
        $ejesPorAnio = [
            1 => [
                [
                    'nombre' => 'Geometría y Magnitudes',
                    'orden'  => 1,
                    'nucleos' => [
                        [
                            'nombre' => 'Cuerpos',
                            'orden'  => 1,
                            'temas'  => [
                                'Sólidos platónicos: tetraedro, cubo, octaedro, dodecaedro, icosaedro',
                                'Prismas, pirámides, cilindros y conos',
                                'Relación de Euler: Caras + Vértices − Aristas',
                                'Posiciones relativas entre planos: perpendicularidad, paralelismo, oblicuidad',
                                'Posiciones relativas entre rectas en el espacio',
                            ],
                        ],
                        [
                            'nombre' => 'Figuras regulares',
                            'orden'  => 2,
                            'temas'  => [
                                'Polígonos regulares',
                                'Inscripción de polígonos regulares en circunferencia',
                                'Ángulos centrales e interiores de polígonos regulares',
                                'Embaldosado del plano con figuras regulares',
                                'Figuras cóncavas y convexas',
                            ],
                        ],
                        [
                            'nombre' => 'Lugar geométrico',
                            'orden'  => 3,
                            'temas'  => [
                                'Mediatrices, circunferencias y elipses como lugar geométrico',
                                'Construcciones con regla, compás y software de geometría dinámica',
                                'Propiedades de triángulos y cuadriláteros',
                            ],
                        ],
                        [
                            'nombre' => 'Medida',
                            'orden'  => 4,
                            'temas'  => [
                                'Recuperación de conocimientos sobre magnitudes (cm², m³, ha)',
                                'Estimación y medición con unidades convencionales',
                                'Equivalencias entre unidades de medida',
                            ],
                        ],
                        [
                            'nombre' => 'Perímetro, Área y Volumen',
                            'orden'  => 5,
                            'temas'  => [
                                'Independencia entre área y perímetro en figuras de igual área',
                                'Fórmulas de área para triángulos y cuadriláteros',
                                'Área del círculo: aproximaciones por polígonos inscritos',
                                'Volumen de cuerpos geométricos',
                                'Equivalencias entre cuerpos de igual volumen',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Número y Operaciones',
                    'orden'  => 2,
                    'nucleos' => [
                        [
                            'nombre' => 'Operaciones con números naturales',
                            'orden'  => 1,
                            'temas'  => [
                                'Propiedades: conmutativa, asociativa, distributiva',
                                'Potenciación y radicación de números naturales',
                                'Propiedades de las potencias',
                                'Estrategias de cálculo mental y escrito',
                                'Uso de calculadora y estimación de resultados',
                            ],
                        ],
                        [
                            'nombre' => 'Divisibilidad',
                            'orden'  => 2,
                            'temas'  => [
                                'Múltiplos y divisores de números naturales',
                                'Números primos y números coprimos',
                                'Mínimo común múltiplo (mcm) y máximo común divisor (mcd)',
                                'Criterios de divisibilidad',
                                'Descomposición en factores primos',
                                'Números amigos, perfectos, abundantes y deficientes',
                                'Conjetura de Goldbach',
                            ],
                        ],
                        [
                            'nombre' => 'Números racionales positivos (Q+)',
                            'orden'  => 3,
                            'temas'  => [
                                'Orden en Q+ y representación en la recta numérica',
                                'La fracción como: cociente, razón, probabilidad, porcentaje y punto en la recta',
                                'Densidad de Q+',
                                'Estimación y cálculo en Q+: algoritmos convencionales',
                                'Redondeo y aproximación de números racionales positivos',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Introducción al Álgebra y al Estudio de las Funciones',
                    'orden'  => 3,
                    'nucleos' => [
                        [
                            'nombre' => 'Lectura, interpretación y construcción de gráficos y tablas',
                            'orden'  => 1,
                            'temas'  => [
                                'Localización mediante coordenadas: mapas y guías',
                                'Representación cartesiana de pares ordenados',
                                'Tablas, diagramas y gráficos estadísticos',
                                'Concepto de variable',
                                'Dependencia e independencia entre variables',
                            ],
                        ],
                        [
                            'nombre' => 'Proporcionalidad',
                            'orden'  => 2,
                            'temas'  => [
                                'Proporcionalidad en situaciones cotidianas: recetas, mezclas, semillas',
                                'Expresiones de proporcionalidad: porcentaje',
                                'Fórmulas de proporcionalidad directa e inversa',
                                'Funciones de dominio discreto',
                            ],
                        ],
                        [
                            'nombre' => 'Iniciación al trabajo algebraico',
                            'orden'  => 3,
                            'temas'  => [
                                'Regularidades en embaldosados, guardas geométricas y sucesiones',
                                'Descubrimiento del término general de una sucesión',
                                'Expresiones algebraicas: 2n+1, n²+1',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Probabilidades y Estadística',
                    'orden'  => 4,
                    'nucleos' => [
                        [
                            'nombre' => 'Fenómenos y experimentos aleatorios',
                            'orden'  => 1,
                            'temas'  => [
                                'Sucesos ciertos, imposibles, contrarios e incompatibles',
                                'Frecuencia de sucesos en experimentos aleatorios',
                                'Probabilidad cualitativa',
                                'Cálculo combinatorio: principio multiplicativo',
                                'Diagramas de árbol y espacios muestrales',
                            ],
                        ],
                        [
                            'nombre' => 'Estadística descriptiva',
                            'orden'  => 2,
                            'temas'  => [
                                'Población y muestra representativa',
                                'Medidas de tendencia central: moda, media aritmética y mediana',
                                'Análisis de encuestas',
                                'Relación entre estadística y probabilidad',
                            ],
                        ],
                    ],
                ],
            ],

            2 => [
                [
                    'nombre' => 'Geometría y Magnitudes',
                    'orden'  => 1,
                    'nucleos' => [
                        [
                            'nombre' => 'Figuras planas: propiedades y construcciones',
                            'orden'  => 1,
                            'temas'  => [
                                'Triángulos: clasificación, propiedades y congruencia (criterios LAL, LLL, ALA)',
                                'Cuadriláteros: paralelogramos, trapecios y trapecios rectángulos',
                                'Circunferencia y círculo: propiedades, arcos, cuerdas y tangentes',
                                'Construcciones con regla y compás',
                                'Uso de software de geometría dinámica',
                            ],
                        ],
                        [
                            'nombre' => 'Transformaciones en el plano',
                            'orden'  => 2,
                            'temas'  => [
                                'Simetría axial y central',
                                'Traslaciones y rotaciones',
                                'Figuras simétricas en el plano cartesiano',
                            ],
                        ],
                        [
                            'nombre' => 'Medida',
                            'orden'  => 3,
                            'temas'  => [
                                'Perímetro y área de figuras planas (ampliación)',
                                'Longitud de la circunferencia y área del círculo',
                                'Estimación y cálculo con unidades de medida de área',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Número y Operaciones',
                    'orden'  => 2,
                    'nucleos' => [
                        [
                            'nombre' => 'Números enteros',
                            'orden'  => 1,
                            'temas'  => [
                                'El conjunto Z: necesidad de los números negativos',
                                'Orden en Z y representación en la recta numérica',
                                'Operaciones en Z: suma, resta, multiplicación y división',
                                'Propiedades de las operaciones en Z',
                                'Valor absoluto de un número entero',
                            ],
                        ],
                        [
                            'nombre' => 'Números racionales (Q)',
                            'orden'  => 2,
                            'temas'  => [
                                'Ampliación de Q a los racionales negativos',
                                'Representación en la recta numérica',
                                'Operaciones con números racionales: algoritmos y propiedades',
                                'Notación decimal: decimales periódicos y no periódicos',
                                'Porcentaje, descuento e interés simple',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Álgebra y Estudio de Funciones',
                    'orden'  => 3,
                    'nucleos' => [
                        [
                            'nombre' => 'Expresiones algebraicas',
                            'orden'  => 1,
                            'temas'  => [
                                'Monomios y polinomios: operaciones básicas',
                                'Productos notables: cuadrado de binomio y diferencia de cuadrados',
                                'Factorización: factor común y diferencia de cuadrados',
                                'Uso de expresiones algebraicas para modelizar situaciones',
                            ],
                        ],
                        [
                            'nombre' => 'Ecuaciones e inecuaciones',
                            'orden'  => 2,
                            'temas'  => [
                                'Ecuaciones de primer grado con una incógnita',
                                'Resolución analítica y verificación',
                                'Inecuaciones de primer grado: resolución y representación',
                                'Problemas que se modelizan con ecuaciones e inecuaciones',
                            ],
                        ],
                        [
                            'nombre' => 'Funciones',
                            'orden'  => 3,
                            'temas'  => [
                                'Concepto de función: relación, tabla, fórmula y gráfico',
                                'Función lineal y función afín',
                                'Proporcionalidad directa como función lineal',
                                'Lectura e interpretación de gráficos de funciones',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Probabilidades y Estadística',
                    'orden'  => 4,
                    'nucleos' => [
                        [
                            'nombre' => 'Estadística',
                            'orden'  => 1,
                            'temas'  => [
                                'Recolección y organización de datos: tablas de frecuencia',
                                'Gráficos estadísticos: barras, histogramas y pictogramas',
                                'Medidas de tendencia central: media, moda y mediana',
                                'Interpretación crítica de datos estadísticos',
                            ],
                        ],
                        [
                            'nombre' => 'Probabilidad',
                            'orden'  => 2,
                            'temas'  => [
                                'Espacio muestral y sucesos',
                                'Probabilidad clásica o de Laplace',
                                'Frecuencia relativa y probabilidad empírica',
                                'Sucesos complementarios, incompatibles y compatibles',
                            ],
                        ],
                    ],
                ],
            ],

            3 => [
                [
                    'nombre' => 'Geometría y Magnitudes',
                    'orden'  => 1,
                    'nucleos' => [
                        [
                            'nombre' => 'Figuras planas',
                            'orden'  => 1,
                            'temas'  => [
                                'Propiedades de figuras planas: revisión y profundización',
                                'Cuadriláteros: clasificación y propiedades',
                                'Circunferencia: ángulos inscriptos y centrales',
                            ],
                        ],
                        [
                            'nombre' => 'Transformaciones en el plano',
                            'orden'  => 2,
                            'temas'  => [
                                'Simetrías, traslaciones y rotaciones: revisión y profundización',
                                'Composición de transformaciones',
                                'Figuras con simetría en el plano cartesiano',
                            ],
                        ],
                        [
                            'nombre' => 'Teorema de Tales, figuras semejantes y homotecia',
                            'orden'  => 3,
                            'temas'  => [
                                'Teorema de Tales: enunciado y aplicaciones',
                                'Semejanza de triángulos: criterios AA, LAL, LLL',
                                'Razón de semejanza: relación entre perímetros y áreas',
                                'Homotecia: propiedades y construcción',
                                'Aplicaciones: escalas en mapas y planos',
                            ],
                        ],
                        [
                            'nombre' => 'Trigonometría',
                            'orden'  => 4,
                            'temas'  => [
                                'Razones trigonométricas: seno, coseno y tangente en triángulo rectángulo',
                                'Cálculo de lados y ángulos en triángulos rectángulos',
                                'Teorema de Pitágoras: generalización y aplicaciones',
                                'Aplicaciones de la trigonometría a problemas de la realidad',
                            ],
                        ],
                        [
                            'nombre' => 'Medida',
                            'orden'  => 5,
                            'temas'  => [
                                'Área de figuras planas: revisión y ampliación',
                                'Área y volumen de sólidos geométricos',
                                'Estimación y precisión en mediciones',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Número y Operaciones',
                    'orden'  => 2,
                    'nucleos' => [
                        [
                            'nombre' => 'Números racionales',
                            'orden'  => 1,
                            'temas'  => [
                                'Representación en la recta numérica: fracciones y decimales',
                                'Formas de expresión: fracción, decimal y notación científica',
                                'Jerarquía de operaciones',
                                'Propiedad distributiva y extracción de factor común',
                                'Aproximación por redondeo y truncamiento',
                                'Densidad de Q: entre dos racionales siempre hay otro',
                                'Errores frecuentes en el trabajo con racionales',
                            ],
                        ],
                        [
                            'nombre' => 'Números reales',
                            'orden'  => 2,
                            'temas'  => [
                                'Integración con Geometría: cálculo de diagonales con Pitágoras',
                                'Reglas de formación para distinguir números irracionales',
                                'Representación de irracionales en la recta numérica',
                                'Operaciones con radicales: simplificación y racionalización',
                                'Segmentos conmensurables e inconmensurables',
                                'Completitud de la recta real: conjunto R',
                                'Notación de intervalos reales',
                                'Módulo (valor absoluto) de un número real',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Álgebra y Estudio de Funciones',
                    'orden'  => 3,
                    'nucleos' => [
                        [
                            'nombre' => 'Trabajo con expresiones algebraicas',
                            'orden'  => 1,
                            'temas'  => [
                                'Reconocimiento de patrones en sucesiones y generalización',
                                'Expresión algebraica del n-ésimo término',
                                'Extracción de factores comunes en expresiones algebraicas',
                            ],
                        ],
                        [
                            'nombre' => 'Funciones: fórmulas, tablas y gráficos',
                            'orden'  => 2,
                            'temas'  => [
                                'Representación de funciones: tablas, fórmulas y gráficos',
                                'Uso de graficadores: representación cartesiana',
                                'Desplazamientos: traslaciones de gráficos de funciones',
                                'Proporcionalidad directa e inversa como tipos de función',
                                'Ecuación general de la recta: y = mx + b',
                                'Rectas paralelas e incidentes: condiciones',
                                'Condiciones para que dos rectas sean paralelas o perpendiculares',
                            ],
                        ],
                        [
                            'nombre' => 'Estudio de funciones',
                            'orden'  => 3,
                            'temas'  => [
                                'Máximos y mínimos de una función',
                                'Ceros de una función',
                                'Intervalos de crecimiento y decrecimiento',
                                'Funciones trigonométricas: seno, coseno y tangente',
                                'Características de funciones trigonométricas: intersecciones, crecimiento, continuidad, periodicidad',
                            ],
                        ],
                        [
                            'nombre' => 'Ecuaciones e inecuaciones',
                            'orden'  => 4,
                            'temas'  => [
                                'Ecuaciones con infinitas soluciones y sin solución',
                                'Inecuaciones: resolución analítica y gráfica',
                                'Ecuaciones e inecuaciones con módulo',
                            ],
                        ],
                        [
                            'nombre' => 'Sistemas de ecuaciones lineales con dos incógnitas',
                            'orden'  => 5,
                            'temas'  => [
                                'Método gráfico: interpretación geométrica',
                                'Método de sustitución e igualación',
                                'Método de suma y resta (Gauss-Jordan)',
                                'Determinantes: regla de Cramer',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Probabilidades y Estadística',
                    'orden'  => 4,
                    'nucleos' => [
                        [
                            'nombre' => 'Estadística — Análisis descriptivo',
                            'orden'  => 1,
                            'temas'  => [
                                'Tablas y gráficos circulares',
                                'Medidas de tendencia central: media, moda y mediana',
                                'Organización de datos por intervalos e histogramas',
                                'Frecuencia absoluta acumulada',
                                'Índice de Desarrollo Humano (IDH) e Índice de Esperanza de Vida',
                            ],
                        ],
                        [
                            'nombre' => 'Combinatoria',
                            'orden'  => 2,
                            'temas'  => [
                                'Diagramas de árbol para contar posibilidades',
                                'Variaciones con y sin repetición: fórmulas',
                                'Combinaciones: fórmula y aplicaciones',
                                'Uso de calculadoras científicas para cálculo combinatorio',
                            ],
                        ],
                        [
                            'nombre' => 'Probabilidad',
                            'orden'  => 3,
                            'temas'  => [
                                'Cálculo combinatorio aplicado a espacios muestrales',
                                'Sucesos compatibles, incompatibles y complementarios',
                                'Probabilidades condicionadas',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($ejesPorAnio as $anioNum => $ejes) {
            $anioStr = $this->anioMap[$anioNum];
            $materia = $this->buscarMateria('Matemática', $anioStr);
            if (! $materia) continue;
            $this->insertar($materia, $anioNum, $ejes);
        }

        $this->command->info('✓ Matemática: curriculum 1°, 2° y 3° año insertado.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // PRÁCTICAS DEL LENGUAJE
    // ─────────────────────────────────────────────────────────────────────
    private function seedPracticasDelLenguaje(): void
    {
        $ejesPorAnio = [
            1 => [
                [
                    'nombre' => 'Ámbito de la Literatura',
                    'orden'  => 1,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de lectura literaria',
                            'orden'  => 1,
                            'temas'  => [
                                'Leer el corpus obligatorio seleccionado para el año',
                                'Formar parte de situaciones sociales de lectura',
                                'Relacionar los textos leídos con otros lenguajes artísticos',
                                'Leer toda clase de textos literarios',
                                'Construir estrategias de lectura para abordar diversos géneros literarios',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de escritura literaria',
                            'orden'  => 2,
                            'temas'  => [
                                'Escribir como lector: producir textos libremente a partir de otros',
                                'Escribir reseñas y recomendaciones literarias',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Ámbito de Estudio',
                    'orden'  => 2,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de lectura para el estudio',
                            'orden'  => 1,
                            'temas'  => [
                                'Buscar información a partir de criterios establecidos por el docente',
                                'Leer textos de estudio con acompañamiento del docente y de los pares',
                                'Resumir textos informativos con distintos propósitos',
                                'Organizar información de textos expositivos en cuadros',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de oralidad y escritura para el estudio',
                            'orden'  => 2,
                            'temas'  => [
                                'Dar cuenta de los conocimientos construidos: exponer formalmente de manera oral',
                                'Exponer por escrito a partir de consignas dadas por el docente',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Ámbito de la Participación Ciudadana',
                    'orden'  => 3,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de oralidad ciudadana',
                            'orden'  => 1,
                            'temas'  => [
                                'Participar en situaciones de intercambio oral sobre temas de interés general del mundo de la cultura',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de lectura crítica de medios',
                            'orden'  => 2,
                            'temas'  => [
                                'Leer críticamente noticias y crónicas periodísticas; comparar entre distintos medios',
                                'Analizar publicidades comerciales, propagandas de salud y de formación ciudadana',
                            ],
                        ],
                        [
                            'nombre' => 'Interacción con medios e instituciones',
                            'orden'  => 3,
                            'temas'  => [
                                'Interactuar críticamente con medios de comunicación e instituciones',
                                'Producir cartas formales',
                                'Leer reglamentos y acuerdos de convivencia',
                            ],
                        ],
                    ],
                ],
            ],

            2 => [
                [
                    'nombre' => 'Ámbito de la Literatura',
                    'orden'  => 1,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de lectura literaria',
                            'orden'  => 1,
                            'temas'  => [
                                'Leer el corpus obligatorio seleccionado para 2° año',
                                'Participar en situaciones sociales de lectura compartida',
                                'Relacionar textos literarios con otros lenguajes artísticos',
                                'Profundizar estrategias de lectura para distintos géneros',
                                'Seguir un género literario a lo largo del año',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de escritura literaria',
                            'orden'  => 2,
                            'temas'  => [
                                'Escribir textos de invención a partir de obras leídas',
                                'Revisar y reescribir producciones propias',
                                'Escribir reseñas con argumentos de valoración',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Ámbito de Estudio',
                    'orden'  => 2,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de lectura para el estudio',
                            'orden'  => 1,
                            'temas'  => [
                                'Buscar, seleccionar y jerarquizar información de diversas fuentes',
                                'Leer textos de estudio de mayor complejidad con autonomía creciente',
                                'Tomar notas y elaborar resúmenes con criterio propio',
                                'Comparar información proveniente de distintas fuentes',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de oralidad y escritura para el estudio',
                            'orden'  => 2,
                            'temas'  => [
                                'Exponer oralmente con apoyo de recursos visuales',
                                'Producir informes y textos expositivos con estructura clara',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Ámbito de la Participación Ciudadana',
                    'orden'  => 3,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de oralidad y debate',
                            'orden'  => 1,
                            'temas'  => [
                                'Participar en intercambios orales sobre problemáticas sociales y culturales',
                                'Argumentar y contraargumentar en situaciones de debate',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de lectura crítica de medios',
                            'orden'  => 2,
                            'temas'  => [
                                'Analizar críticamente textos periodísticos de opinión: editorial y columna',
                                'Comparar el tratamiento de una noticia en distintos medios',
                                'Reconocer estrategias persuasivas en publicidades y propagandas',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de escritura ciudadana',
                            'orden'  => 3,
                            'temas'  => [
                                'Producir textos de opinión: carta de lectores y nota de opinión',
                                'Leer y analizar textos normativos: reglamentos y estatutos',
                            ],
                        ],
                    ],
                ],
            ],

            3 => [
                [
                    'nombre' => 'Ámbito de la Literatura',
                    'orden'  => 1,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de lectura literaria',
                            'orden'  => 1,
                            'temas'  => [
                                'Leer el corpus obligatorio seleccionado para 3° año',
                                'Formar parte de situaciones sociales de lectura con mayor autonomía',
                                'Relacionar textos literarios con otros lenguajes artísticos y con el contexto de producción',
                                'Seguir un autor: leer varias obras e identificar rasgos de su poética',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de escritura literaria',
                            'orden'  => 2,
                            'temas'  => [
                                'Escribir como lector: breves ensayos de interpretación',
                                'Producir textos de invención que expliciten el diálogo con las obras leídas',
                                'Revisar y reescribir producciones atendiendo al efecto estético buscado',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Ámbito de Estudio',
                    'orden'  => 2,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de lectura para el estudio',
                            'orden'  => 1,
                            'temas'  => [
                                'Buscar y seleccionar información de manera autónoma en distintas fuentes',
                                'Resumir textos argumentativos identificando tesis y argumentos',
                                'Ampliar textos con información de otras fuentes',
                                'Leer textos académicos de mayor complejidad',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de oralidad y escritura para el estudio',
                            'orden'  => 2,
                            'temas'  => [
                                'Exponer oralmente con recursos gráficos: infografías y esquemas',
                                'Escribir textos de estudio: monografía e informe de investigación',
                            ],
                        ],
                    ],
                ],
                [
                    'nombre' => 'Ámbito de la Participación Ciudadana',
                    'orden'  => 3,
                    'nucleos' => [
                        [
                            'nombre' => 'Prácticas de oralidad y debate',
                            'orden'  => 1,
                            'temas'  => [
                                'Organizar y participar en debates formales sobre temas polémicos',
                                'Analizar información explícita e implícita en textos polémicos',
                            ],
                        ],
                        [
                            'nombre' => 'Prácticas de lectura crítica y escritura ciudadana',
                            'orden'  => 2,
                            'temas'  => [
                                'Leer críticamente discursos jurídicos y políticos',
                                'Producir textos para comunicarse con instituciones: petitorios y proyectos',
                                'Analizar estrategias de manipulación en textos mediáticos',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($ejesPorAnio as $anioNum => $ejes) {
            $anioStr = $this->anioMap[$anioNum];
            $materia = $this->buscarMateria('Prácticas del Lenguaje', $anioStr);
            if (! $materia) continue;
            $this->insertar($materia, $anioNum, $ejes);
        }

        $this->command->info('✓ Prácticas del Lenguaje: curriculum 1°, 2° y 3° año insertado.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Busca la materia global (user_id IS NULL) para el nombre y año dados.
     * Si no existe global, toma la primera que encuentre.
     */
    private function buscarMateria(string $nombre, string $anioStr): ?Materia
    {
        $materia = Materia::where('nombre', $nombre)
                          ->where('anio', $anioStr)
                          ->whereNull('user_id')
                          ->first();

        if (! $materia) {
            $materia = Materia::where('nombre', $nombre)
                              ->where('anio', $anioStr)
                              ->first();
        }

        if (! $materia) {
            $this->command->warn("⚠ No se encontró: {$nombre} {$anioStr}");
        }

        return $materia;
    }

    /**
     * Inserta ejes → núcleos → temas usando firstOrCreate (idempotente).
     */
    private function insertar(Materia $materia, int $anioNum, array $ejes): void
    {
        foreach ($ejes as $ejeDatos) {
            $eje = EjeCurricular::firstOrCreate(
                [
                    'materia_id' => $materia->id,
                    'nombre'     => $ejeDatos['nombre'],
                    'anio'       => $anioNum,
                ],
                ['orden' => $ejeDatos['orden']]
            );

            foreach ($ejeDatos['nucleos'] as $nucleoDatos) {
                $nucleo = NucleoCurricular::firstOrCreate(
                    ['eje_curricular_id' => $eje->id, 'nombre' => $nucleoDatos['nombre']],
                    ['orden' => $nucleoDatos['orden']]
                );

                foreach ($nucleoDatos['temas'] as $orden => $nombreTema) {
                    TemaCurricular::firstOrCreate(
                        ['nucleo_curricular_id' => $nucleo->id, 'nombre' => $nombreTema],
                        ['orden' => $orden + 1]
                    );
                }
            }
        }
    }
}
