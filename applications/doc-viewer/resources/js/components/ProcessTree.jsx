import React from 'react';
import { Tree, Tag } from 'antd';
import { ApartmentOutlined, FileOutlined } from '@ant-design/icons';

// Maps for colors and icons
const statusColor = {
  running: 'green',
  stopped: 'red',
  pending: 'orange',
};

const criticalityColor = {
  low: 'blue',
  medium: 'orange',
  high: 'red',
};

const typeIcon = {
  process: <ApartmentOutlined />,
  task: <FileOutlined />,
};

// Convert flat list of nodes to Ant Design treeData
function buildTreeData(nodes) {
  const map = new Map();
  nodes.forEach((n) => map.set(n.id, { ...n, children: [] }));
  const tree = [];
  map.forEach((node) => {
    if (node.parentId) {
      const parent = map.get(node.parentId);
      if (parent) {
        parent.children.push(node);
      } else {
        tree.push(node);
      }
    } else {
      tree.push(node);
    }
  });

  const transform = (node) => ({
    key: node.id,
    icon: typeIcon[node.type],
    title: (
      <span>
        {node.name}{' '}
        <Tag color={statusColor[node.status] || 'default'}>{node.status}</Tag>
        <Tag color={criticalityColor[node.criticality] || 'default'}>{node.criticality}</Tag>
        <Tag color="geekblue">{node.type}</Tag>
      </span>
    ),
    children: node.children.map(transform),
  });

  return tree.map(transform);
}

const ProcessTree = ({ nodes }) => {
  const treeData = React.useMemo(() => buildTreeData(nodes), [nodes]);
  return <Tree showIcon defaultExpandAll treeData={treeData} />;
};

export { buildTreeData };
export default ProcessTree;
