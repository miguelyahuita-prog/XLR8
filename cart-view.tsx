"use client";

import { useEffect, useMemo, useState } from "react";
import { useRouter } from "next/navigation";
import { money } from "@/lib/format";

type CartItem = {
  id: number;
  name: string;
  price: string;
  imageUrl: string | null;
  quantity: number;
};

export function CartView() {
  const router = useRouter();
  const [items, setItems] = useState<CartItem[]>([]);

  useEffect(() => {
    setItems(JSON.parse(localStorage.getItem("flores_alesli_cart") ?? "[]"));
  }, []);

  function persist(next: CartItem[]) {
    setItems(next);
    localStorage.setItem("flores_alesli_cart", JSON.stringify(next));
  }

  const total = useMemo(() => items.reduce((sum, item) => sum + Number(item.price) * item.quantity, 0), [items]);

  if (!items.length) {
    return <div className="notice">Tu carrito esta vacio. El catalogo esta listo cuando quieras elegir un arreglo.</div>;
  }

  return (
    <div className="split">
      <div className="card">
        <table className="table">
          <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
          <tbody>
            {items.map((item) => (
              <tr key={item.id}>
                <td>{item.name}</td>
                <td>
                  <input
                    aria-label="Cantidad"
                    type="number"
                    min="1"
                    value={item.quantity}
                    onChange={(event) => persist(items.map((row) => row.id === item.id ? { ...row, quantity: Number(event.target.value) } : row))}
                    style={{ width: 76 }}
                  />
                </td>
                <td>{money(Number(item.price) * item.quantity)}</td>
                <td><button className="button ghost" onClick={() => persist(items.filter((row) => row.id !== item.id))}>Quitar</button></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <aside className="card card-body">
        <h2>Total</h2>
        <p className="price">{money(total)}</p>
        <button className="button" onClick={() => router.push("/checkout")}>Continuar al checkout</button>
      </aside>
    </div>
  );
}
