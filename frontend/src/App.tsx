import { Routes, Route } from 'react-router-dom'
import AreaTable from './components/AreaTable'
import AreaTree from './components/AreaTree'
import { useAreaTree } from './hooks/useAreaTree'

export default function App() {
  const { data = [] } = useAreaTree()

  return (
    <Routes>
      <Route path="/" element={<AreaTree />} />
      <Route path="/areas" element={<AreaTable data={data} />} />
    </Routes>
  )
}
