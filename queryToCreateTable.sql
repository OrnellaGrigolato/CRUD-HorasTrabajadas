CREATE TABLE CRM_HORAS_TRABAJADAS (
    id INT IDENTITY(1,1) PRIMARY KEY,   -- Definición del campo autoincremental y clave primaria
    cliente NVARCHAR(255) NOT NULL,     -- Cliente al que se le realizó el trabajo
    tipo NVARCHAR(255) NOT NULL,        -- (Abonado, Facturable,...)
	horaDeInicio TIME,					-- Hora en que se inició el trabajo
	cantidad DECIMAL(4,2),				-- Cantidad de horas trabajadas (acepta como máximo 99.99)
    comentario NVARCHAR(255) NOT NULL,  -- Comentarios sobre el trabajo realizado
    ID_Usuario INT,						-- Id del trabajador
	fechaDeInicio DATE,					-- Fecha en la que se realizó el trabajo
    CONSTRAINT FK_HorasTrabajadas_Usuario FOREIGN KEY (ID_Usuario)
        REFERENCES CRM_VENDEDORES(id_vendedor)
);

CREATE TABLE [dbo].[CRM_Horas](
	[ID_Hora] [int] IDENTITY(1,1) NOT NULL,
	[ID_Cliente] [int] NOT NULL,
	[ID_Vendedor] [int] NOT NULL,
	[FechaCarga] [datetime] NOT NULL,
	[FechaInicio] [datetime] NOT NULL,
	[Horas] [decimal](18, 2) NOT NULL,
	[ID_Proyecto] [int] NULL, --dejar null
	[ID_TipoHora] [varchar](3) NOT NULL, --ABO - FAC
	[Comentario] [varchar](8000) NOT NULL,
	[N_FACTURA] [varchar](50) NULL, --DEJAR NULL
	[IMPORTE] [float] NULL, --DEJAR NULL
	[N_PEDIDO] [varchar](50) NULL, --DEJAR NULL
	[CONTROLADO] [bit] NULL, --DEJAR NULL
	[ENVIO_CLIENTE_PARA_CONTROL] [varchar](50) NULL, --DEJAR NULL
 CONSTRAINT [PK_CRM_Horas] PRIMARY KEY CLUSTERED 
(
	[ID_Hora] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 90, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ALTER TABLE [dbo].[CRM_Horas] ADD  CONSTRAINT [DF_CRM_Horas_CONTROLADO]  DEFAULT ((0)) FOR [CONTROLADO]
GO

ALTER TABLE [dbo].[CRM_Horas]  WITH CHECK ADD  CONSTRAINT [FK_CRM_Horas_CRM_CLIENTES] FOREIGN KEY([ID_Cliente])
REFERENCES [dbo].[CRM_CLIENTES] ([ID_Cliente])
GO

ALTER TABLE [dbo].[CRM_Horas] CHECK CONSTRAINT [FK_CRM_Horas_CRM_CLIENTES]
GO

ALTER TABLE [dbo].[CRM_Horas]  WITH CHECK ADD  CONSTRAINT [FK_CRM_Horas_CRM_Proyectos] FOREIGN KEY([ID_Proyecto])
REFERENCES [dbo].[CRM_Proyectos] ([ID_Proyecto])
GO