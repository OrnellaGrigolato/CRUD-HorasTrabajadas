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