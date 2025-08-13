import React from 'react';
import { Graph } from '@ant-design/graphs';

// Convert backend nodes to graph data for @ant-design/graphs
export function buildGraphData(nodes) {
  const gNodes = nodes.map((n) => ({ id: n.id, label: n.name }));
  const edges = nodes
    .filter((n) => n.parentId)
    .map((n) => ({ source: n.parentId, target: n.id }));
  return { nodes: gNodes, edges };
}

const ProcessGraph = ({ nodes }) => {
  const data = React.useMemo(() => buildGraphData(nodes), [nodes]);
  const config = {
    data,
    autoFit: true,
    behaviors: ['zoom-canvas', 'drag-canvas', 'drag-node'],
    layout: { type: 'dagre' },
    nodeCfg: {
      size: 26,
    },
  };
  return <Graph {...config} />;
};

export default ProcessGraph;
