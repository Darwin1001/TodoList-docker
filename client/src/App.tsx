import { FC } from 'react';
import TodoList from './components/Todo/TodoList';
import './App.css';

const App: FC = () => {
  return (
    <div className="App">
      <h1>Ma Todo List</h1>
      <TodoList />
    </div>
  );
}

export default App;
