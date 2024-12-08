/* 
 * ----------------------------------------------------------------------------
 * Consulta de Funcionários: 
 * ----------------------------------------------------------------------------
 * Escreva uma query para listar todos os funcionários ativos, mostrando as 
 * colunas id, nome, salario. Ordene o resultado pelo nome em ordem ascendente.
 * ----------------------------------------------------------------------------
 */
select 
	t.id_vendedor	as id,
	t.nome,
	t.salario
from 
	vendedores as t 
where 
	t.inativo = false
order by
	t.nome ASC;


/*
 * ----------------------------------------------------------------------------
 * Funcionários com Salário Acima da Média: 
 * ----------------------------------------------------------------------------
 * Escreva uma query para listar os funcionários que possuem um salário acima 
 * da média salarial de todos os funcionários. A consulta deve mostrar as 
 * colunas id, nome, e salario, ordenadas pelo salario em ordem descendente.
 * ----------------------------------------------------------------------------
 */
with cte_media as(
	-- calcula a media salarial
	select
		avg(t.salario)::numeric(10, 2) as media_salarial
	from 
		vendedores as t 
	where 
		t.inativo = false
)
select 
	t.id_vendedor	as id,
	t.nome,
	t.salario
from 
	vendedores as t 
	cross join cte_media as m
where
	t.salario >= m.media_salarial
order by
	t.salario desc;


/*
 * ----------------------------------------------------------------------------
 * Resumo por cliente: 
 * ----------------------------------------------------------------------------
 * Escreva uma query para listar todos os clientes e o valor total de pedidos 
 * já transmitidos. A consulta deve retornar as colunas id, razao_social, 
 * total, ordenadas pelo total em ordem descendente.
 * ----------------------------------------------------------------------------
 */
select 
	c.id_cliente as id,
	c.razao_social,
	coalesce(sum(p.valor_total), 0) as total
from 
	clientes as c 	 	
	left join pedido as p on p.id_cliente = c.id_cliente
group by
	c.id_cliente,
	c.razao_social
order by 3 desc;


/*
 * ----------------------------------------------------------------------------
 * Situação por pedido: 
 * ----------------------------------------------------------------------------
 * Escreva uma query que retorne a situação atual de cada pedido da base. A 
 * consulta deve retornar as colunas id, valor, data e situacao. A situacao 
 * deve obedecer a seguinte regra:
 * - Se possui data de cancelamento preenchido: CANCELADO
 * - Se possui data de faturamento preenchido: FATURADO
 * - Caso não possua data de cancelamento e nem faturamento: PENDENTE
 * ----------------------------------------------------------------------------
 */
select 
	p.id_pedido			as id,
	p.valor_total		as valor,
	p.data_emissao		as data,
	case 
	  when p.data_cancelamento is not null then 'CANCELADO'
	  when p.data_faturamento is not null then 'FATURADO'
	  else 'PENDENTE'
	end					as situacao
from 
	pedido as p;

/*
 * ----------------------------------------------------------------------------
 * Produtos mais vendidos: 
 * ----------------------------------------------------------------------------
 * Escreva uma query que retorne o produto mais vendido ( em quantidade ), 
 * incluindo o valor total vendido deste produto, quantidade de pedidos em que 
 * ele apareceu e para quantos clientes diferentes ele foi vendido. A consulta 
 * deve retornar as colunas id_produto, quantidade_vendida, total_vendido, 
 * clientes, pedidos. Caso haja empate em quantidade de vendas, utilizar o 
 * total vendido como critério de desempate.
 * ----------------------------------------------------------------------------
*/	
select 
	pr.id_produto, 
	sum(ip.quantidade) as quantidade_vendida,
	sum(ip.quantidade*ip.preco_praticado) as total_vendido,
	count(distinct ip.id_pedido) as pedidos,
	count(distinct pe.id_cliente) as clientes 
	
from 
	produtos as pr
	join itens_pedido as ip on ip.id_produto = pr.id_produto
	join pedido as pe on pe.id_pedido = ip.id_pedido
group by 
	pr.id_produto
order by 
	quantidade_vendida desc,
	total_vendido desc 
limit 1
	